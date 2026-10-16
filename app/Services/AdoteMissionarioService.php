<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Missionario;
use App\Models\Regiao;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdoteMissionarioService
{
    public const JMN_API_URL = 'https://missionarios.jmnipb.org.br/api/missionarios?ordem=nome_asc';

    public const JMN_BASE_URL = 'https://missionarios.jmnipb.org.br';

    public const APMT_LIST_URL = 'https://apmt.org.br/missionarios';

    public static function store(Request $request): Missionario
    {
        DB::beginTransaction();
        try {
            $missionario = Missionario::create(self::dadosBasicos($request));

            if ($request->hasFile('foto')) {
                $missionario->update([
                    'foto' => self::salvarFoto($request, $missionario->id),
                ]);
            }

            DB::commit();

            return $missionario;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function update(Missionario $missionario, Request $request): Missionario
    {
        DB::beginTransaction();
        try {
            $dados = self::dadosBasicos($request);

            if ($request->hasFile('foto')) {
                self::removerFoto($missionario);
                $dados['foto'] = self::salvarFoto($request, $missionario->id);
            }

            $missionario->update($dados);

            DB::commit();

            return $missionario->fresh();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function delete(Missionario $missionario): void
    {
        DB::beginTransaction();
        try {
            self::removerFoto($missionario);
            $missionario->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function adotar(Missionario $missionario): void
    {
        $campo = UserService::getCampoInstanciaDB();

        if (empty($campo['campo']) || empty($campo['id'])) {
            throw new Exception('Instância não identificada para adoção.');
        }

        if (!in_array($campo['campo'], ['sinodal_id', 'federacao_id'], true)) {
            throw new Exception('Perfil sem permissão para adotar missionário.');
        }

        DB::beginTransaction();
        try {
            $jaAdotou = Missionario::query()
                ->where($campo['campo'], $campo['id'])
                ->lockForUpdate()
                ->exists();

            if ($jaAdotou) {
                throw new Exception('Sua instância já adotou um missionário.');
            }

            $missionario = Missionario::query()
                ->whereKey($missionario->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($missionario->estaAdotado()) {
                throw new Exception('Este missionário já foi adotado.');
            }

            $missionario->update([
                $campo['campo'] => $campo['id'],
                'adotado_em' => now(),
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function desfazerAdocao(Missionario $missionario): void
    {
        DB::beginTransaction();
        try {
            $missionario->update([
                'sinodal_id' => null,
                'federacao_id' => null,
                'adotado_em' => null,
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public static function removerTodosVinculos(): int
    {
        DB::beginTransaction();
        try {
            $afetados = Missionario::query()
                ->where(function ($q) {
                    $q->whereNotNull('sinodal_id')->orWhereNotNull('federacao_id');
                })
                ->update([
                    'sinodal_id' => null,
                    'federacao_id' => null,
                    'adotado_em' => null,
                ]);

            DB::commit();

            return $afetados;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * @return array{criados: int, atualizados: int, total: int}
     */
    public static function sincronizarJmn(): array
    {
        $response = Http::timeout(60)->acceptJson()->get(self::JMN_API_URL);

        if (!$response->successful()) {
            throw new Exception('Não foi possível obter os missionários da JMN.');
        }

        $lista = $response->json('missionarios');

        if (!is_array($lista) || empty($lista)) {
            throw new Exception('A API da JMN não retornou missionários.');
        }

        $estados = Estado::query()->get()->keyBy(fn (Estado $e) => strtoupper($e->sigla));
        $criados = 0;
        $atualizados = 0;

        DB::beginTransaction();
        try {
            foreach ($lista as $item) {
                $estadoSigla = strtoupper((string) ($item['estado'] ?? ''));
                $estado = $estados->get($estadoSigla);

                if (!$estado) {
                    continue;
                }

                $dados = [
                    'nome' => $item['nome'] ?? 'Sem nome',
                    'cidade' => $item['cidade'] ?? ($item['campo'] ?? 'Não informado'),
                    'estado_id' => $estado->id,
                    'regiao_id' => $estado->regiao_id,
                    'pais' => 'Brasil',
                    'foto' => self::montarUrlFotoJmn($item['foto'] ?? null),
                    'whatsapp' => $item['telefone'] ?? null,
                    'email' => $item['email'] ?? null,
                    'outras_informacoes' => self::montarOutrasInformacoesJmn($item),
                ];

                $existente = Missionario::withTrashed()->where('jmn_id', $item['id'])->first();

                if ($existente) {
                    if ($existente->trashed()) {
                        $existente->restore();
                    }
                    $existente->update($dados);
                    $atualizados++;
                } else {
                    Missionario::create(array_merge($dados, [
                        'jmn_id' => $item['id'],
                    ]));
                    $criados++;
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return [
            'criados' => $criados,
            'atualizados' => $atualizados,
            'total' => $criados + $atualizados,
        ];
    }

    /**
     * Extrai missionários do __NEXT_DATA__ embutido em https://apmt.org.br/missionarios
     *
     * @return array{criados: int, atualizados: int, total: int}
     */
    public static function sincronizarApmt(): array
    {
        $response = Http::timeout(90)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; CNM-Estatistica/1.0)',
                'Accept' => 'text/html,application/xhtml+xml',
            ])
            ->get(self::APMT_LIST_URL);

        if (!$response->successful()) {
            throw new Exception('Não foi possível acessar o site da APMT.');
        }

        $lista = self::extrairMissionariosApmtDoHtml($response->body());

        if (empty($lista)) {
            throw new Exception('Não foi possível localizar a lista de missionários no site da APMT.');
        }

        $criados = 0;
        $atualizados = 0;

        DB::beginTransaction();
        try {
            foreach ($lista as $item) {
                $apmtId = (string) ($item['id'] ?? '');
                if ($apmtId === '') {
                    continue;
                }

                $pais = $item['country']['name'] ?? 'Não informado';
                $cidade = trim((string) ($item['locale'] ?? ''));
                if ($cidade === '') {
                    $cidade = $pais;
                }

                $dados = [
                    'nome' => $item['name'] ?? 'Sem nome',
                    'cidade' => $cidade,
                    'estado_id' => null,
                    'regiao_id' => null,
                    'pais' => $pais,
                    'foto' => $item['photo']['_url'] ?? null,
                    'whatsapp' => !empty($item['phone']) ? $item['phone'] : null,
                    'email' => $item['email'] ?? null,
                    'outras_informacoes' => self::montarOutrasInformacoesApmt($item),
                ];

                $existente = Missionario::withTrashed()->where('apmt_id', $apmtId)->first();

                if ($existente) {
                    if ($existente->trashed()) {
                        $existente->restore();
                    }
                    $existente->update($dados);
                    $atualizados++;
                } else {
                    Missionario::create(array_merge($dados, [
                        'apmt_id' => $apmtId,
                    ]));
                    $criados++;
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return [
            'criados' => $criados,
            'atualizados' => $atualizados,
            'total' => $criados + $atualizados,
        ];
    }

    public static function getRegioes(): Collection
    {
        return Regiao::query()->orderBy('nome')->pluck('nome', 'id');
    }

    public static function getEstados(?int $regiaoId = null): Collection
    {
        return Estado::query()
            ->when($regiaoId, fn ($q) => $q->where('regiao_id', $regiaoId))
            ->orderBy('nome')
            ->get()
            ->mapWithKeys(fn (Estado $estado) => [
                $estado->id => $estado->sigla . ' - ' . $estado->nome,
            ]);
    }

    public static function getEstadosPorRegiao(): array
    {
        return Estado::query()
            ->orderBy('nome')
            ->get()
            ->groupBy('regiao_id')
            ->map(fn ($estados) => $estados->mapWithKeys(fn (Estado $estado) => [
                $estado->id => $estado->sigla . ' - ' . $estado->nome,
            ]))
            ->toArray();
    }

    public static function missionarioDaInstancia(): ?Missionario
    {
        $campo = UserService::getCampoInstanciaDB();

        if (empty($campo['campo']) || empty($campo['id'])) {
            return null;
        }

        if (!in_array($campo['campo'], ['sinodal_id', 'federacao_id'], true)) {
            return null;
        }

        return Missionario::query()
            ->with(['estado', 'regiao', 'sinodal', 'federacao'])
            ->where($campo['campo'], $campo['id'])
            ->first();
    }

    public static function disponiveis(?int $regiaoId = null, ?string $agencia = null): Collection
    {
        return Missionario::query()
            ->with(['estado', 'regiao'])
            ->disponiveis()
            ->when($regiaoId, fn ($q) => $q->where('regiao_id', $regiaoId))
            ->daAgencia($agencia)
            ->orderBy('nome')
            ->get();
    }

    private static function dadosBasicos(Request $request): array
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'estado_id' => ['required', 'exists:estados,id'],
            'regiao_id' => ['required', 'exists:regioes,id'],
            'pais' => ['required', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'outras_informacoes' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:5120'],
        ]);

        $estado = Estado::findOrFail($request->estado_id);

        if ((int) $estado->regiao_id !== (int) $request->regiao_id) {
            throw new Exception('A UF selecionada não pertence à região informada.');
        }

        return [
            'nome' => $request->nome,
            'cidade' => $request->cidade,
            'estado_id' => $request->estado_id,
            'regiao_id' => $request->regiao_id,
            'pais' => $request->pais,
            'whatsapp' => $request->whatsapp,
            'email' => $request->email,
            'outras_informacoes' => $request->outras_informacoes,
        ];
    }

    private static function salvarFoto(Request $request, string $missionarioId): string
    {
        $extensao = $request->file('foto')->getClientOriginalExtension();
        $nome = Str::slug(pathinfo($request->file('foto')->getClientOriginalName(), PATHINFO_FILENAME));
        $nomeArquivo = $nome . '-' . time() . '.' . $extensao;
        $path = $request->file('foto')->storeAs("public/missionarios/{$missionarioId}", $nomeArquivo);

        return str_replace('public', 'storage', $path);
    }

    private static function removerFoto(Missionario $missionario): void
    {
        if (empty($missionario->foto) || $missionario->fotoEhExterna()) {
            return;
        }

        Storage::delete(str_replace('storage', 'public', $missionario->foto));
    }

    private static function montarUrlFotoJmn(?string $foto): ?string
    {
        if (empty($foto)) {
            return null;
        }

        if (Str::startsWith($foto, ['http://', 'https://'])) {
            return $foto;
        }

        return rtrim(self::JMN_BASE_URL, '/') . '/' . ltrim($foto, '/');
    }

    private static function montarOutrasInformacoesJmn(array $item): ?string
    {
        $partes = [];

        if (!empty($item['titulo']) || !empty($item['cargo_label'])) {
            $partes[] = trim(($item['titulo'] ?? '') . ' ' . ($item['cargo_label'] ?? ''));
        }

        if (!empty($item['campo'])) {
            $partes[] = 'Campo: ' . $item['campo'];
        }

        if (!empty($item['nome_conjuge'])) {
            $partes[] = 'Cônjuge: ' . $item['nome_conjuge'];
        }

        if (!empty($item['parceria'])) {
            $partes[] = 'Parceria: ' . $item['parceria'];
        }

        if (!empty($item['status'])) {
            $partes[] = 'Status JMN: ' . $item['status'];
        }

        $partes[] = 'Origem: JMN';

        return implode("\n", $partes);
    }

    private static function extrairMissionariosApmtDoHtml(string $html): array
    {
        if (!preg_match('/<script id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $matches)) {
            return [];
        }

        $payload = json_decode($matches[1], true);
        $lista = $payload['props']['pageProps']['missionarys'] ?? null;

        return is_array($lista) ? $lista : [];
    }

    private static function montarOutrasInformacoesApmt(array $item): ?string
    {
        $partes = ['Origem: APMT'];

        if (!empty($item['slug'])) {
            $partes[] = 'Perfil: https://apmt.org.br/missionarios/' . $item['slug'];
        }

        if (!empty($item['status']['label'])) {
            $partes[] = 'Status: ' . $item['status']['label'];
        }

        if (!empty($item['position']['label'])) {
            $partes[] = 'Posição: ' . $item['position']['label'];
        }

        if (!empty($item['continent']['name'])) {
            $partes[] = 'Continente: ' . $item['continent']['name'];
        }

        if (!empty($item['nameSpouse'])) {
            $partes[] = 'Cônjuge: ' . $item['nameSpouse'];
        }

        if (!empty($item['entryYear'])) {
            $partes[] = 'Ingresso: ' . $item['entryYear'];
        }

        if (!empty($item['instagram'])) {
            $partes[] = 'Instagram: ' . $item['instagram'];
        }

        if (!empty($item['youTube'])) {
            $partes[] = 'YouTube: ' . $item['youTube'];
        }

        if (!empty($item['facebook'])) {
            $partes[] = 'Facebook: ' . $item['facebook'];
        }

        if (!empty($item['projectDocument']['_url'])) {
            $partes[] = 'Projeto: ' . $item['projectDocument']['_url'];
        }

        return implode("\n", $partes);
    }
}
