<?php

namespace App\Services;

use App\Models\Digesto;
use App\Models\TipoReuniao;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DigestoService
{
    public const PATH_DIR = 'storage/disgesto/';
    public const PATH_SERVER = 'public/disgesto/';

    public const TIPO_RELATORIO_GESTAO = 'relatorio_gestao';
    public const TIPO_PROPOSTA = 'proposta';
    public const TIPO_CONSULTA = 'consulta';
    public const TIPO_RELATORIO_COMISSAO = 'relatorio_comissao';
    public const TIPO_OUTRO = 'outro';

    private const SNIPPET_TAMANHO = 180;
    private const SNIPPET_CONTEXTO = 40;
    private const POR_PAGINA = 10;

    public static function store(Request $request): Digesto
    {
        try {
            $path = $request->arquivo->store('public/disgesto');

            return Digesto::create(array_merge(
                self::dadosCadastro($request),
                ['path' => '/' . str_replace('public', 'storage', $path)]
            ));
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public static function update(Digesto $digesto, Request $request): Digesto
    {
        try {
            $digesto->update(self::dadosCadastro($request));

            if ($request->hasFile('arquivo')) {
                $path = $request->arquivo->store('public/disgesto');
                $digesto->update([
                    'path' => '/' . str_replace('public', 'storage', $path)
                ]);
            }

            return $digesto;
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public static function delete(Digesto $digesto): void
    {
        try {
            $digesto->delete();
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public static function getTipos(): array
    {
        return TipoReuniao::all()->pluck('nome', 'id')->toArray();
    }

    public static function getTiposDocumento(): array
    {
        return [
            self::TIPO_RELATORIO_GESTAO => 'Relatório de Gestão',
            self::TIPO_RELATORIO_COMISSAO => 'Relatório de Comissão',
            self::TIPO_PROPOSTA => 'Proposta',
            self::TIPO_CONSULTA => 'Consulta',
            self::TIPO_OUTRO => 'Outro',
        ];
    }

    public static function labelTipoDocumento(?string $tipo, bool $curto = false): string
    {
        if ($tipo === null || $tipo === '') {
            return '';
        }

        $curtos = [
            self::TIPO_RELATORIO_GESTAO => 'Gestão',
            self::TIPO_RELATORIO_COMISSAO => 'Comissão',
            self::TIPO_PROPOSTA => 'Proposta',
            self::TIPO_CONSULTA => 'Consulta',
            self::TIPO_OUTRO => 'Outro',
        ];

        if ($curto) {
            return $curtos[$tipo] ?? (self::getTiposDocumento()[$tipo] ?? $tipo);
        }

        return self::getTiposDocumento()[$tipo] ?? $tipo;
    }

    public static function getAnos(): array
    {
        return Digesto::query()
            ->select('ano')
            ->distinct()
            ->orderByDesc('ano')
            ->pluck('ano')
            ->all();
    }

    public static function getComissoes(): array
    {
        return Digesto::query()
            ->whereNotNull('comissao')
            ->where('comissao', '!=', '')
            ->select('comissao')
            ->distinct()
            ->orderBy('comissao')
            ->pluck('comissao')
            ->all();
    }

    public static function estaIncompleto(Digesto $digesto): bool
    {
        return ! filled($digesto->tipo_documento)
            || ! filled($digesto->numero_documento);
    }

    public static function aplicarIncompleto(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('tipo_documento')
                ->orWhere('tipo_documento', '')
                ->orWhereNull('numero_documento')
                ->orWhere('numero_documento', '');
        });
    }

    public static function contagemCompletude(): array
    {
        $total = Digesto::count();
        $incompletos = self::aplicarIncompleto(Digesto::query())->count();

        return [
            'total' => $total,
            'incompletos' => $incompletos,
        ];
    }

    public static function dadosArquivo(Digesto $digesto): array
    {
        $nome = basename((string) $digesto->path);
        $absoluto = public_path(ltrim((string) $digesto->path, '/'));
        $existe = filled($digesto->path) && is_file($absoluto);
        $extensao = strtolower(pathinfo($nome, PATHINFO_EXTENSION));

        return [
            'nome' => $nome,
            'existe' => $existe,
            'tamanho' => $existe ? self::formatarTamanho((int) filesize($absoluto)) : null,
            'enviado_em' => optional($digesto->created_at)->format('d/m/Y'),
            'url' => $digesto->path,
            'previsivel' => $existe && $extensao === 'pdf',
        ];
    }

    private static function formatarTamanho(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024) . ' KB';
        }

        return round($bytes / 1048576, 1) . ' MB';
    }

    public static function buscar(): array
    {
        $request = request();
        $buscou = self::temFiltros($request);
        $chave = trim((string) $request->input('chave', ''));
        $ordenar = (string) $request->input('ordenar', $chave !== '' ? 'relevancia' : 'recente');
        $anosSelecionados = self::anosSelecionados($request);
        $tiposSelecionados = self::valoresLista($request->input('tipos_documento', []));
        $comissoesSelecionadas = self::valoresLista($request->input('comissoes', []));

        if (! $buscou) {
            return [
                'buscou' => false,
                'resultados' => new Paginator([], 0, self::POR_PAGINA),
                'facets' => ['tipos_documento' => [], 'anos' => [], 'comissoes' => []],
                'chips' => [],
                'anos_disponiveis' => self::getAnos(),
                'ordenar' => $ordenar,
                'total' => 0,
                'chave' => $chave,
                'anos_selecionados' => $anosSelecionados,
                'tipos_selecionados' => $tiposSelecionados,
                'comissoes_selecionadas' => $comissoesSelecionadas,
            ];
        }

        $base = self::queryBusca($request, false);
        $facets = self::montarFacets($base, $anosSelecionados, $tiposSelecionados, $comissoesSelecionadas);
        $resultados = self::paginar(self::queryBusca($request, true), $request, $chave, $ordenar);

        return [
            'buscou' => true,
            'resultados' => $resultados,
            'facets' => $facets,
            'chips' => self::montarChips($request, $chave, $anosSelecionados, $tiposSelecionados, $comissoesSelecionadas),
            'anos_disponiveis' => self::getAnos(),
            'ordenar' => $ordenar,
            'total' => $resultados->total(),
            'chave' => $chave,
            'anos_selecionados' => $anosSelecionados,
            'tipos_selecionados' => $tiposSelecionados,
            'comissoes_selecionadas' => $comissoesSelecionadas,
        ];
    }

    public static function temFiltrosAtivos(): bool
    {
        return self::temFiltros(request());
    }

    public static function exportarCsv(): StreamedResponse
    {
        $request = request();
        $chave = trim((string) $request->input('chave', ''));
        $query = self::queryBusca($request, true);

        $nome = 'digesto-resultados-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query, $chave) {
            $saida = fopen('php://output', 'w');
            fwrite($saida, "\xEF\xBB\xBF");
            fputcsv($saida, [
                'Título',
                'Tipo de documento',
                'Reunião',
                'Ano',
                'Número',
                'Comissão',
                'Trecho',
                'URL',
            ], ';');

            $query->chunkById(100, function (Collection $itens) use ($saida, $chave) {
                foreach ($itens as $item) {
                    $snippet = html_entity_decode(
                        strip_tags(self::montarSnippet($item->texto, $chave)),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    fputcsv($saida, [
                        $item->titulo,
                        self::labelTipoDocumento($item->tipo_documento),
                        $item->tipo->nome ?? '',
                        $item->ano,
                        $item->numero_documento,
                        $item->comissao,
                        $snippet,
                        url(route('digesto.exibir', self::pathExibir($item->path))),
                    ], ';');
                }
            });

            fclose($saida);
        }, $nome, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public static function formatarCitacao(Digesto $item): string
    {
        $partes = [rtrim((string) $item->titulo, '.') . '.'];

        $reuniao = $item->tipo->nome ?? null;
        if ($reuniao) {
            $partes[] = $reuniao . ', ' . $item->ano . '.';
        } elseif ($item->ano) {
            $partes[] = $item->ano . '.';
        }

        if (filled($item->numero_documento)) {
            $partes[] = 'Doc. ' . $item->numero_documento . '.';
        }

        return implode(' ', $partes);
    }

    /**
     * Método que verifica se o arquivo é doc ou docx e ao inves de
     * exibir (arquivo binário está sendo exibido) força o download
     *
     * @param string $path
     * @return mixed
     */
    public static function exibir(string $path)
    {
        $posicaoDoc = strpos($path, '.doc');
        if ($posicaoDoc) {
            $novoNome = 'digesto_' . date('ymdhis') . substr($path, $posicaoDoc);
            return response()->download(self::PATH_DIR . $path, $novoNome);
        }
        if (! file_exists(self::PATH_DIR.$path)) {
            abort(404, 'Aquivo não encontrado!');
        }
        return response()->file(self::PATH_DIR . $path);
    }

    /**
     * @deprecated Use buscar()
     */
    public static function buscarItem(): array
    {
        return self::buscar()['resultados']->items();
    }

    private static function dadosCadastro(Request $request): array
    {
        $tipoDocumento = $request->input('tipo_documento');
        if (! array_key_exists((string) $tipoDocumento, self::getTiposDocumento())) {
            $tipoDocumento = null;
        }

        return [
            'tipo_reuniao_id' => $request->tipo_reuniao_id,
            'titulo' => $request->titulo,
            'ano' => $request->ano,
            'texto' => $request->texto,
            'tipo_documento' => $tipoDocumento,
            'numero_documento' => self::textoOpcional($request->input('numero_documento')),
            'comissao' => self::textoOpcional($request->input('comissao')),
        ];
    }

    private static function textoOpcional($valor): ?string
    {
        $texto = trim((string) $valor);

        return $texto !== '' ? $texto : null;
    }

    private static function temFiltros(Request $request): bool
    {
        return $request->anyFilled(['tipo_reuniao', 'ano', 'chave', 'busca'])
            || $request->filled('anos')
            || $request->filled('tipos_documento')
            || $request->filled('comissoes');
    }

    private static function anosSelecionados(Request $request): array
    {
        $anos = self::valoresLista($request->input('anos', []));

        if ($request->filled('ano')) {
            $anos[] = (string) $request->input('ano');
        }

        return array_values(array_unique($anos));
    }

    private static function valoresLista($valor): array
    {
        return array_values(array_filter(array_map('strval', (array) $valor), function ($item) {
            return $item !== '';
        }));
    }

    private static function queryBusca(Request $request, bool $incluirFacets): Builder
    {
        $query = Digesto::query()->with('tipo');
        $chave = trim((string) $request->input('chave', ''));

        if ($request->filled('tipo_reuniao')) {
            $query->where('tipo_reuniao_id', $request->tipo_reuniao);
        }

        if ($chave !== '') {
            $termo = self::formatarTermoFullText($chave);
            $like = '%' . self::escaparLike($chave) . '%';

            $query->where(function (Builder $q) use ($chave, $termo, $like) {
                if ($termo !== '') {
                    $q->whereRaw('MATCH(titulo, texto) AGAINST(? IN BOOLEAN MODE)', [$termo]);
                }

                $q->orWhere('numero_documento', 'like', $like)
                    ->orWhere('titulo', 'like', $like);
            });
        }

        if ($incluirFacets) {
            $anos = self::anosSelecionados($request);
            if ($anos) {
                $query->whereIn('ano', $anos);
            }

            $tipos = self::valoresLista($request->input('tipos_documento', []));
            if ($tipos) {
                $query->whereIn('tipo_documento', $tipos);
            }

            $comissoes = self::valoresLista($request->input('comissoes', []));
            if ($comissoes) {
                $query->whereIn('comissao', $comissoes);
            }
        }

        return $query;
    }

    private static function ordenarQuery(Builder $query, Request $request, string $chave, string $ordenar): Builder
    {
        if ($ordenar === 'relevancia' && $chave !== '') {
            $termo = self::formatarTermoFullText($chave);
            if ($termo !== '') {
                return $query->orderByRaw(
                    'MATCH(titulo, texto) AGAINST(? IN BOOLEAN MODE) DESC',
                    [$termo]
                )->orderByDesc('ano');
            }
        }

        if ($ordenar === 'numero') {
            return $query->orderByRaw('numero_documento IS NULL')
                ->orderBy('numero_documento')
                ->orderBy('titulo');
        }

        return $query->orderByDesc('ano')->orderBy('titulo');
    }

    private static function paginar(Builder $query, Request $request, string $chave, string $ordenar): LengthAwarePaginator
    {
        $paginator = self::ordenarQuery($query, $request, $chave, $ordenar)
            ->paginate(self::POR_PAGINA)
            ->withQueryString();

        $paginator->getCollection()->transform(function (Digesto $item) use ($chave) {
            $item->path_exibir = self::pathExibir($item->path);
            $item->snippet = self::montarSnippet($item->texto, $chave);
            $item->titulo_html = $chave !== ''
                ? self::destacar($item->titulo, $chave)
                : e($item->titulo);
            $item->citacao = self::formatarCitacao($item);
            $item->tipo_documento_label = self::labelTipoDocumento($item->tipo_documento, true);
            $item->tipo_documento_classe = $item->tipo_documento ?: '';

            return $item;
        });

        return $paginator;
    }

    private static function montarFacets(
        Builder $base,
        array $anosSelecionados,
        array $tiposSelecionados,
        array $comissoesSelecionadas
    ): array {
        $tipos = (clone $base)
            ->setEagerLoads([])
            ->reorder()
            ->whereNotNull('tipo_documento')
            ->select('tipo_documento')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('tipo_documento')
            ->pluck('total', 'tipo_documento');

        $anos = (clone $base)
            ->setEagerLoads([])
            ->reorder()
            ->select('ano')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('ano')
            ->orderByDesc('ano')
            ->pluck('total', 'ano');

        $comissoes = (clone $base)
            ->setEagerLoads([])
            ->reorder()
            ->whereNotNull('comissao')
            ->where('comissao', '!=', '')
            ->select('comissao')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('comissao')
            ->orderBy('comissao')
            ->pluck('total', 'comissao');

        $facetTipos = [];
        foreach (self::getTiposDocumento() as $valor => $label) {
            if (! isset($tipos[$valor])) {
                continue;
            }
            $facetTipos[] = [
                'valor' => $valor,
                'label' => $label,
                'total' => (int) $tipos[$valor],
                'ativo' => in_array($valor, $tiposSelecionados, true),
                'url' => self::urlToggleLista('tipos_documento', $valor),
            ];
        }

        $facetAnos = [];
        foreach ($anos as $ano => $total) {
            $valor = (string) $ano;
            $facetAnos[] = [
                'valor' => $valor,
                'label' => $valor,
                'total' => (int) $total,
                'ativo' => in_array($valor, $anosSelecionados, true),
                'url' => self::urlToggleLista('anos', $valor, ['ano']),
            ];
        }

        $facetComissoes = [];
        foreach ($comissoes as $comissao => $total) {
            $valor = (string) $comissao;
            $facetComissoes[] = [
                'valor' => $valor,
                'label' => $valor,
                'total' => (int) $total,
                'ativo' => in_array($valor, $comissoesSelecionadas, true),
                'url' => self::urlToggleLista('comissoes', $valor),
            ];
        }

        return [
            'tipos_documento' => $facetTipos,
            'anos' => $facetAnos,
            'comissoes' => $facetComissoes,
        ];
    }

    private static function montarChips(
        Request $request,
        string $chave,
        array $anosSelecionados,
        array $tiposSelecionados,
        array $comissoesSelecionadas
    ): array {
        $chips = [];
        $tiposReuniao = self::getTipos();

        if ($request->filled('tipo_reuniao')) {
            $id = $request->input('tipo_reuniao');
            $chips[] = [
                'label' => $tiposReuniao[$id] ?? 'Reunião',
                'url' => self::urlSem(['tipo_reuniao']),
            ];
        }

        foreach ($anosSelecionados as $ano) {
            $chips[] = [
                'label' => $ano,
                'url' => self::urlToggleLista('anos', $ano, ['ano']),
            ];
        }

        if ($chave !== '') {
            $chips[] = [
                'label' => '"' . $chave . '"',
                'url' => self::urlSem(['chave']),
            ];
        }

        foreach ($tiposSelecionados as $tipo) {
            $chips[] = [
                'label' => self::labelTipoDocumento($tipo, true) ?: $tipo,
                'url' => self::urlToggleLista('tipos_documento', $tipo),
            ];
        }

        foreach ($comissoesSelecionadas as $comissao) {
            $chips[] = [
                'label' => $comissao,
                'url' => self::urlToggleLista('comissoes', $comissao),
            ];
        }

        return $chips;
    }

    public static function urlSem(array $chaves): string
    {
        $query = request()->query();
        foreach ($chaves as $chave) {
            unset($query[$chave]);
        }
        unset($query['page']);

        return route('digesto', $query);
    }

    public static function urlCom(array $valores): string
    {
        $query = array_merge(request()->query(), $valores);
        unset($query['page']);

        return route('digesto', $query);
    }

    public static function urlToggleLista(string $chave, string $valor, array $remover = []): string
    {
        $query = request()->query();
        foreach ($remover as $extra) {
            unset($query[$extra]);
        }

        $atual = self::valoresLista($query[$chave] ?? []);

        if ($chave === 'anos' && request()->filled('ano') && ! in_array((string) request('ano'), $atual, true)) {
            $atual[] = (string) request('ano');
        }

        if (in_array($valor, $atual, true)) {
            $atual = array_values(array_filter($atual, fn ($item) => $item !== $valor));
        } else {
            $atual[] = $valor;
        }

        if ($atual) {
            $query[$chave] = $atual;
        } else {
            unset($query[$chave]);
        }

        unset($query['page']);

        return route('digesto', $query);
    }

    private static function pathExibir(?string $path): string
    {
        return str_replace('/' . self::PATH_DIR, '', (string) $path);
    }

    private static function montarSnippet(?string $texto, string $chave): string
    {
        $texto = trim(preg_replace('/\s+/u', ' ', (string) $texto) ?? '');

        if ($texto === '') {
            return 'Documento sem trecho de prévia disponível — clique em "Ver documento" para o conteúdo completo.';
        }

        if ($chave === '') {
            $trecho = mb_substr($texto, 0, self::SNIPPET_TAMANHO);
            if (mb_strlen($texto) > self::SNIPPET_TAMANHO) {
                $trecho .= '...';
            }

            return e($trecho);
        }

        $inicio = mb_stripos($texto, $chave);
        if ($inicio === false) {
            foreach (self::palavrasChave($chave) as $palavra) {
                $inicio = mb_stripos($texto, $palavra);
                if ($inicio !== false) {
                    break;
                }
            }
        }

        if ($inicio === false) {
            return 'Documento sem trecho de prévia disponível — clique em "Ver documento" para o conteúdo completo.';
        }

        $start = max(0, $inicio - self::SNIPPET_CONTEXTO);
        $trecho = mb_substr($texto, $start, self::SNIPPET_TAMANHO);
        if ($start > 0) {
            $trecho = '...' . $trecho;
        }
        if (($start + self::SNIPPET_TAMANHO) < mb_strlen($texto)) {
            $trecho .= '...';
        }

        return self::destacar($trecho, $chave);
    }

    private static function destacar(string $texto, string $chave): string
    {
        $escaped = e($texto);
        $palavras = self::palavrasChave($chave);

        if ($palavras === []) {
            return $escaped;
        }

        usort($palavras, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

        $padrao = implode('|', array_map(fn ($palavra) => preg_quote($palavra, '/'), $palavras));

        return preg_replace('/(' . $padrao . ')/iu', '<mark>$1</mark>', $escaped) ?? $escaped;
    }

    private static function palavrasChave(string $chave): array
    {
        $chave = trim($chave);
        if ($chave === '') {
            return [];
        }

        if (preg_match('/^".+"$/', $chave)) {
            $frase = trim($chave, '"');

            return $frase !== '' ? [$frase] : [];
        }

        $palavras = preg_split('/\s+/', $chave, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $palavras[] = $chave;

        return array_values(array_unique(array_filter($palavras, fn ($palavra) => mb_strlen($palavra) >= 2)));
    }

    private static function escaparLike(string $valor): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $valor);
    }

    /**
     * Monta o termo da busca FULLTEXT em BOOLEAN MODE.
     * Cada palavra vira +palavra* (obrigatória e com prefixo).
     */
    private static function formatarTermoFullText(string $chave): string
    {
        $chave = trim($chave);

        if ($chave === '') {
            return '';
        }

        if (preg_match('/^".+"$/', $chave)) {
            $frase = trim($chave, '"');
            $frase = preg_replace('/[+\-><()~*@]+/', ' ', $frase);
            $frase = trim(preg_replace('/\s+/', ' ', $frase));

            return $frase !== '' ? '"' . $frase . '"' : '';
        }

        $termos = preg_split('/\s+/', $chave, -1, PREG_SPLIT_NO_EMPTY);

        return collect($termos)
            ->map(function (string $termo) {
                $termo = preg_replace('/[+\-><()~*"@]+/', '', $termo);

                if ($termo === '' || mb_strlen($termo) < 3) {
                    return null;
                }

                return '+' . $termo . '*';
            })
            ->filter()
            ->implode(' ');
    }
}
