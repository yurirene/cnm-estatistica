<?php

namespace App\Services;

use App\Enums\ResolucaoOrigem;
use App\Enums\ResolucaoPrioridade;
use App\Enums\ResolucaoStatus;
use App\Models\Resolucao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use League\Csv\Reader;

class ResolucaoService
{
    public const DIAS_ALERTA_ANTECEDENCIA = 7;

    public static function queryParaUsuario(?User $user = null)
    {
        $user = $user ?? Auth::user();
        $query = Resolucao::query()->with(['responsavel:id,name,email', 'criador:id,name,email']);

        if (!self::isGestor($user)) {
            $query->where('responsavel_id', $user->id);
        }

        return $query;
    }

    public static function estatisticas(?User $user = null): array
    {
        $base = self::queryParaUsuario($user);
        $hoje = now()->toDateString();
        $limiteProximas = now()->addDays(self::DIAS_ALERTA_ANTECEDENCIA)->toDateString();

        $stats = (clone $base)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as concluidas', [ResolucaoStatus::Concluido->value])
            ->selectRaw(
                'SUM(CASE WHEN status NOT IN (?, ?) AND prazo_final IS NOT NULL AND prazo_final < ? THEN 1 ELSE 0 END) as atrasadas',
                [ResolucaoStatus::Concluido->value, ResolucaoStatus::Cancelado->value, $hoje]
            )
            ->selectRaw(
                'SUM(CASE WHEN status NOT IN (?, ?) AND prazo_final IS NOT NULL AND prazo_final >= ? AND prazo_final <= ? THEN 1 ELSE 0 END) as proximas',
                [ResolucaoStatus::Concluido->value, ResolucaoStatus::Cancelado->value, $hoje, $limiteProximas]
            )
            ->first();

        return [
            'total' => (int) ($stats->total ?? 0),
            'concluidas' => (int) ($stats->concluidas ?? 0),
            'atrasadas' => (int) ($stats->atrasadas ?? 0),
            'proximas' => (int) ($stats->proximas ?? 0),
        ];
    }

    public static function criar(array $dados, ?User $criador = null, array $anexos = []): Resolucao
    {
        $criador = $criador ?? Auth::user();

        $resolucao = Resolucao::create([
            'titulo' => $dados['titulo'],
            'descricao' => $dados['descricao'],
            'origem' => $dados['origem'],
            'status' => $dados['status'] ?? ResolucaoStatus::Pendente->value,
            'prioridade' => $dados['prioridade'] ?? ResolucaoPrioridade::Media->value,
            'data_aprovacao' => $dados['data_aprovacao'],
            'prazo_final' => $dados['prazo_final'] ?? null,
            'responsavel_id' => $dados['responsavel_id'] ?? null,
            'numero' => self::gerarNumero(),
            'criado_por' => $criador->id,
            'anexos' => self::processarUploadAnexos($anexos) ?: null,
        ]);

        self::notificarResponsavel($resolucao->fresh(['responsavel']));

        return $resolucao;
    }

    public static function atualizar(Resolucao $resolucao, array $dados, array $anexos = [], array $removerAnexos = []): Resolucao
    {
        if (!self::isGestor()) {
            unset($dados['responsavel_id']);
        }

        $anexosAtuais = $resolucao->anexos ?? [];

        if (!empty($removerAnexos)) {
            $anexosAtuais = self::removerAnexos($anexosAtuais, $removerAnexos);
        }

        if (!empty($anexos)) {
            $anexosAtuais = array_merge($anexosAtuais, self::processarUploadAnexos($anexos));
        }

        $dados['anexos'] = $anexosAtuais ?: null;

        $resolucao->update($dados);

        return $resolucao->fresh(['responsavel', 'criador']);
    }

    public static function excluir(Resolucao $resolucao): void
    {
        self::apagarAnexosFisicos($resolucao->anexos ?? []);
        $resolucao->delete();
    }

    public static function importarCsv(UploadedFile $arquivo, ?User $criador = null): array
    {
        $criador = $criador ?? Auth::user();
        $reader = Reader::createFromPath($arquivo->getRealPath());
        $reader->setHeaderOffset(0);
        $reader->setDelimiter(self::detectarDelimitador($arquivo->getRealPath()));

        $importados = 0;
        $erros = [];

        foreach ($reader->getRecords() as $indice => $linha) {
            $numeroLinha = $indice + 2;

            try {
                $dados = self::normalizarLinhaImportacao($linha);

                if (empty($dados['titulo'])) {
                    continue;
                }

                self::criar($dados, $criador);
                $importados++;
            } catch (\Throwable $th) {
                $erros[] = "Linha {$numeroLinha}: {$th->getMessage()}";
            }
        }

        return compact('importados', 'erros');
    }

    public static function alertarPrazos(): int
    {
        $hoje = now()->toDateString();
        $limite = now()->addDays(self::DIAS_ALERTA_ANTECEDENCIA)->toDateString();
        $enviados = 0;

        $resolucoes = Resolucao::query()
            ->with('responsavel')
            ->whereNotIn('status', [ResolucaoStatus::Concluido->value, ResolucaoStatus::Cancelado->value])
            ->whereNotNull('prazo_final')
            ->where(function ($query) use ($hoje, $limite) {
                $query->whereDate('prazo_final', '<', $hoje)
                    ->orWhereBetween('prazo_final', [$hoje, $limite]);
            })
            ->where(function ($query) use ($hoje) {
                $query->whereNull('ultimo_alerta_prazo_em')
                    ->orWhereDate('ultimo_alerta_prazo_em', '<', $hoje);
            })
            ->get();

        foreach ($resolucoes as $resolucao) {
            $responsavel = $resolucao->responsavel;

            if (empty($responsavel?->telegram_chat_id)) {
                continue;
            }

            $mensagem = self::montarMensagemAlertaPrazo($resolucao);

            if (TelegramService::sendMessage($responsavel->telegram_chat_id, $mensagem)) {
                $resolucao->update(['ultimo_alerta_prazo_em' => $hoje]);
                $enviados++;
            }
        }

        return $enviados;
    }

    public static function getResponsaveis(): Collection
    {
        return User::query()
            ->where('status', 1)
            ->whereHas('role', function ($query) {
                $query->whereIn('slug', [
                    User::ROLE_ADMINISTRADOR,
                    User::ROLE_SEC_EXECUTIVA,
                    User::ROLE_DIRETORIA,
                    'presidente',
                    'secretariado_comum',
                ]);
            })
            ->when(request('term'), function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->limit(30)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'text' => "{$user->name} ({$user->email})",
            ]);
    }

    public static function opcoesEnums(): array
    {
        return [
            'origens' => collect(ResolucaoOrigem::cases())->mapWithKeys(
                fn (ResolucaoOrigem $o) => [$o->value => Str::title($o->value)]
            )->all(),
            'status' => collect(ResolucaoStatus::cases())->mapWithKeys(
                fn (ResolucaoStatus $s) => [$s->value => self::labelStatus($s)]
            )->all(),
            'prioridades' => collect(ResolucaoPrioridade::cases())->mapWithKeys(
                fn (ResolucaoPrioridade $p) => [$p->value => Str::title($p->value)]
            )->all(),
        ];
    }

    public static function labelStatus(ResolucaoStatus $status): string
    {
        return match ($status) {
            ResolucaoStatus::Pendente => 'Pendente',
            ResolucaoStatus::EmAndamento => 'Em andamento',
            ResolucaoStatus::Concluido => 'Concluído',
            ResolucaoStatus::Cancelado => 'Cancelado',
        };
    }

    public static function isGestor(?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        return Gate::forUser($user)->allows('isAdmin')
            || $user->role?->slug === User::ROLE_SEC_EXECUTIVA
            || $user->role?->slug === User::ROLE_DIRETORIA;
    }

    public static function gerarNumero(): string
    {
        $ano = now()->year;
        $ultimo = Resolucao::query()
            ->where('numero', 'like', "RES-{$ano}/%")
            ->orderByDesc('id')
            ->value('numero');

        $sequencia = 1;

        if ($ultimo && preg_match('/RES-\d{4}\/(\d+)$/', $ultimo, $matches)) {
            $sequencia = (int) $matches[1] + 1;
        }

        return sprintf('RES-%d/%03d', $ano, $sequencia);
    }

    public static function notificarResponsavel(Resolucao $resolucao): void
    {
        $responsavel = $resolucao->responsavel;

        if (empty($responsavel?->telegram_chat_id)) {
            return;
        }

        $mensagem = "Nova resolução atribuída a você:\n"
            . "<b>{$resolucao->numero}</b> — {$resolucao->titulo}\n"
            . 'Prazo: ' . ($resolucao->prazo_final?->format('d/m/Y') ?? 'Não definido');

        TelegramService::sendMessage($responsavel->telegram_chat_id, $mensagem);
    }

    protected static function montarMensagemAlertaPrazo(Resolucao $resolucao): string
    {
        $prazo = $resolucao->prazo_final->format('d/m/Y');
        $tipo = $resolucao->esta_atrasado
            ? '⚠️ <b>ATRASADA</b>'
            : '⏰ <b>Prazo próximo</b>';

        return "{$tipo}\n"
            . "<b>{$resolucao->numero}</b> — {$resolucao->titulo}\n"
            . "Prazo final: {$prazo}\n"
            . 'Status: ' . self::labelStatus($resolucao->status);
    }

    protected static function normalizarLinhaImportacao(array $linha): array
    {
        $linha = array_change_key_case($linha, CASE_LOWER);
        $email = trim($linha['responsavel_email'] ?? $linha['email_responsavel'] ?? '');
        $responsavelId = null;

        if ($email !== '') {
            $responsavel = User::query()->where('email', $email)->first();

            if (!$responsavel) {
                throw new InvalidArgumentException("Responsável não encontrado para o e-mail: {$email}");
            }

            $responsavelId = $responsavel->id;
        }

        $origem = strtolower(trim($linha['origem'] ?? ''));
        $status = strtolower(trim($linha['status'] ?? ResolucaoStatus::Pendente->value));
        $prioridade = strtolower(trim($linha['prioridade'] ?? ResolucaoPrioridade::Media->value));

        if (!in_array($origem, array_column(ResolucaoOrigem::cases(), 'value'), true)) {
            throw new InvalidArgumentException("Origem inválida: {$origem}");
        }

        return [
            'titulo' => trim($linha['titulo'] ?? ''),
            'descricao' => trim($linha['descricao'] ?? ''),
            'origem' => $origem,
            'status' => in_array($status, array_column(ResolucaoStatus::cases(), 'value'), true)
                ? $status
                : ResolucaoStatus::Pendente->value,
            'prioridade' => in_array($prioridade, array_column(ResolucaoPrioridade::cases(), 'value'), true)
                ? $prioridade
                : ResolucaoPrioridade::Media->value,
            'data_aprovacao' => self::parseData($linha['data_aprovacao'] ?? ''),
            'prazo_final' => !empty($linha['prazo_final']) ? self::parseData($linha['prazo_final']) : null,
            'responsavel_id' => $responsavelId,
        ];
    }

    protected static function parseData(string $valor): string
    {
        $valor = trim($valor);

        if (empty($valor)) {
            throw new InvalidArgumentException('Data obrigatória não informada.');
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y'] as $formato) {
            try {
                return Carbon::createFromFormat($formato, $valor)->format('Y-m-d');
            } catch (\Throwable) {
            }
        }

        throw new InvalidArgumentException("Data inválida: {$valor}");
    }

    protected static function detectarDelimitador(string $caminho): string
    {
        $primeiraLinha = fgets(fopen($caminho, 'r'));

        return str_contains($primeiraLinha, ';') ? ';' : ',';
    }

    protected static function processarUploadAnexos(array $arquivos): array
    {
        $paths = [];

        foreach ($arquivos as $arquivo) {
            if ($arquivo instanceof UploadedFile) {
                $paths[] = $arquivo->store('resolucoes/anexos', 'public');
            }
        }

        return $paths;
    }

    protected static function removerAnexos(array $anexosAtuais, array $pathsRemover): array
    {
        $pathsRemover = array_values(array_intersect($anexosAtuais, $pathsRemover));
        self::apagarAnexosFisicos($pathsRemover);

        return array_values(array_diff($anexosAtuais, $pathsRemover));
    }

    protected static function apagarAnexosFisicos(array $paths): void
    {
        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }
    }
}
