<?php

namespace App\Services\Apps;

use App\Helpers\FormHelper;
use App\Models\Apps\Tesouraria\Lancamento;
use App\Models\Apps\Tesouraria\Categoria;
use App\Services\Instancias\DiretoriaService;
use App\Services\UserService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class TesourariaService
{
    public const MODELO_PLANILHA = 'planilha';
    public const MODELO_MOVIMENTOS = 'movimentos';
    public const MODELO_ENTRADA_SAIDA = 'entrada-saida';
    public const MODELO_CATEGORIAS = 'categorias';

    public const MODELOS_RELATORIO = [
        self::MODELO_PLANILHA => 'Planilha original',
        self::MODELO_MOVIMENTOS => 'Movimentos',
        self::MODELO_ENTRADA_SAIDA => 'Entrada e saída',
        self::MODELO_CATEGORIAS => 'Categorias',
    ];

    private const CORES_RECEITA = ['#14917A', '#0E7C66', '#1AA88A', '#127A62', '#3CB89A', '#0B6350'];
    private const CORES_DESPESA = ['#C4601F', '#E27D3A', '#B4531A', '#D4783A', '#A34712', '#F0A06A'];
    private const COR_SEM_CATEGORIA = '#9AA3B2';

    /**
     * Salva um novo lançamento
     *
     * @param array $dados
     *
     * @return Lancamento|null
     */
    public static function store(array $dados): ?Lancamento
    {
        $campo = UserService::getCampoInstanciaDB();
        $lancamento = Lancamento::create([
            'descricao' => $dados['descricao'],
            'data_lancamento' => $dados['data_lancamento'],
            'valor' => FormHelper::converterParaFloat($dados['valor']),
            'tipo' => $dados['tipo'],
            'categoria_id' => $dados['categoria_id'],
            $campo['campo'] => $campo['id']
        ]);

        if (!empty($dados['comprovante'])) {
            $path = self::salvarComprovante($campo, $dados['comprovante']);
            $lancamento->update([
                'comprovante' => $path,
            ]);
        }

        return $lancamento;
    }

    /**
     * Atualiza um lançamento
     *
     * @param array $dados
     * @param Lancamento $lancamento
     *
     * @return Lancamento
     */
    public static function update(array $dados, Lancamento $lancamento): Lancamento
    {
        $campo = UserService::getCampoInstanciaDB();
        $lancamento->update([
            'descricao' => $dados['descricao'],
            'data_lancamento' => $dados['data_lancamento'],
            'valor' => FormHelper::converterParaFloat($dados['valor']),
            'tipo' => $dados['tipo'],
            'categoria_id' => $dados['categoria_id']
        ]);

        if (!empty($dados['comprovante'])) {
            $path = self::salvarComprovante($campo, $dados['comprovante'], $lancamento->comprovante);
            $lancamento->update([
                'comprovante' => $path,
            ]);
        }

        return $lancamento;
    }

    /**
     * Remove um lançamento
     *
     * @param Lancamento $lancamento
     *
     * @return void
     */
    public static function delete(Lancamento $lancamento): void
    {
        $path = $lancamento->comprovante;
        $lancamento->delete();
        if (!empty($path)) {
            Storage::delete(
                str_replace(
                    'storage',
                    'public',
                    $path
                )
            );
        }
     }

    /**
     * Método para salvar comprovante
     *
     * @param UploadedFile $file
     * @param string|null $pathAntigo
     *
     * @return void
     */
    public static function salvarComprovante(array $campo, UploadedFile $file, ?string $pathAntigo = null): string
    {
        if (!empty($pathAntigo)) {
            Storage::delete(
                str_replace(
                    'storage',
                    'public',
                    $pathAntigo
                )
            );
        }

        $nome = time().'.'. $file->getClientOriginalExtension();
        $pasta = str_replace('_id', '', $campo['campo']);
        $diretorioCompleto = "/public/{$pasta}/{$campo['id']}/tesouraria/" . date('Y');

        // Garantir que o diretório existe com permissões corretas
        if (!Storage::exists($diretorioCompleto)) {
            Storage::createDirectory($diretorioCompleto, [
                'visibility' => 'public',
            ]);
        }

        $path = $file->storeAs($diretorioCompleto, $nome);

        return str_replace(
            'public',
            'storage',
            $path
        );
    }

    /**
     * retorna as categorias para um select
     *
     * @return array
     */
    public static function categoriaToSelect(): array
    {
        $campo = UserService::getCampoInstanciaDB();
        return Categoria::all()
            ->where($campo['campo'], $campo['id'])
            ->pluck('nome', 'id')
            ->toArray();
    }


    /**
     * Salva uma nova categoria
     *
     * @param array $dados
     *
     * @return Categoria|null
     */
    public static function salvarCategoria(array $dados): ?Categoria
    {
        $campo = UserService::getCampoInstanciaDB();

        return Categoria::create([
            'nome' => $dados['nome'],
            $campo['campo'] => $campo['id']
        ]);
    }

    /**
     * Atualiza uma categoria
     *
     * @param array $dados
     * @param Categoria $categoria
     *
     * @return Categoria
     */
    public static function atualizarCategoria(array $dados, Categoria $categoria): Categoria
    {
        $categoria->update([
            'nome' => $dados['nome'],
        ]);

        return $categoria;
    }

    /**
     * Remove uma categoria
     *
     * @param Categoria $categoria
     *
     * @return void
     */
    public static function removerCategoria(Categoria $categoria): void
    {
        $categoria->delete();
    }

    /**
     * Retorna os tipos de lançamentos
     *
     * @return array
     */
    public static function getTipos(): array
    {
        return Lancamento::TIPOS;
    }

    public static function totalizadores(?array $filtro = []): array
    {
        $ultimoDiaDoMesAnterior = !empty($filtro['dataInicio'])
            ? Carbon::createFromFormat('d/m/Y', $filtro['dataInicio'])
            : Carbon::now()->subMonth()->lastOfMonth();

        $primeiroDia = !empty($filtro['dataInicio'])
            ? Carbon::createFromFormat('d/m/Y', $filtro['dataInicio'])
            : Carbon::now()->firstOfMonth();

        $ultimoDia = !empty($filtro['dataFim'])
            ? Carbon::createFromFormat('d/m/Y', $filtro['dataFim'])
            : Carbon::now()->lastOfMonth();

        $saldoInicial = self::getTotalAte($ultimoDiaDoMesAnterior);

        $entradas = self::getTotalAte(
            $ultimoDia,
            $primeiroDia,
            Lancamento::TIPO_ENTRADA
        );
        $saidas = self::getTotalAte(
            $ultimoDia,
            $primeiroDia,
            Lancamento::TIPO_SAIDA
        );

        $saldoFinal = $saldoInicial + $entradas + $saidas;

        return [
            'saldoInicial' => number_format($saldoInicial, 2, ',', '.'),
            'entradas' => number_format($entradas, 2, ',', '.'),
            'saidas' => number_format($saidas, 2, ',', '.'),
            'saldoFinal' => number_format($saldoFinal, 2, ',', '.')
        ];
    }

    /**
     * Undocumented function
     *
     * @param DateTime $ultimoDia
     * @param DateTime|null $primeiroDia
     * @param integer|null $tipo
     *
     * @return float
     */
    public static function getTotalAte(
        DateTime $ultimoDia,
        ?DateTime $primeiroDia = null,
        ?int $tipo = null
    ): float {
        $lancamentos = Lancamento::where('data_lancamento', '<=', $ultimoDia)
            ->when(!is_null($tipo), function ($sql) use ($tipo) {
                return $sql->where('tipo', '=', $tipo);
            })
            ->when(!is_null($primeiroDia), function ($sql) use ($primeiroDia) {
                return $sql->where('data_lancamento', '>=', $primeiroDia);
            })
            ->get();
        $total = 0;

        foreach ($lancamentos as $lancamento) {
            $valor = $lancamento->getRawOriginal('valor');

            if ($lancamento->tipo == Lancamento::TIPO_SAIDA) {
                $valor = $valor * -1;
            }

            $total += $valor;
        }

        return (float) $total;
    }

    public static function getAnosTesouraria(): array
    {
        return Lancamento::select(DB::raw('YEAR(data_lancamento)'))
            ->groupBy('YEAR(data_lancamento)')
            ->get()
            ->pluck('YEAR(data_lancamento)', 'YEAR(data_lancamento)')
            ->toArray();
    }

    /**
     * Panorama do topo: anos com lançamento, mês corrente e data do último lançamento.
     * O saldo inicial de cada ano é o saldo acumulado até o ano anterior.
     *
     * @return array
     */
    public static function panorama(): array
    {
        $ultimaData = Lancamento::query()->max('data_lancamento');
        $mesAtual = Carbon::now()->format('Y-m');
        $faixaVazia = [
            'rotulo' => self::rotuloMes($mesAtual, true),
            'valor' => $mesAtual,
            'entradas' => self::formatarMoeda(0),
            'saidas' => self::formatarMoeda(0),
            'saldo' => self::formatarMoeda(0),
            'saldoClasse' => '',
        ];

        if (empty($ultimaData)) {
            $zerado = self::montarEscopo(0, 0, 0, 0);

            return [
                'geral' => $zerado,
                'mes' => $zerado,
                'anos' => [],
                'ano' => null,
                'faixa' => $faixaVazia,
                'atualizadoAte' => null,
            ];
        }

        $corte = Carbon::parse($ultimaData)->startOfDay();
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();
        $anos = self::escoposPorAno();
        $anoCorrente = Carbon::now()->format('Y');
        $anoSelecionado = $anos === []
            ? null
            : (array_key_exists($anoCorrente, $anos) ? $anoCorrente : (string) array_key_first($anos));

        if ($corte->lt($inicioMes)) {
            $ateCorte = self::somarMovimento(null, $corte->toDateString());
            $saldoAnterior = $ateCorte['entradas'] - $ateCorte['saidas'];
            $movimentoMes = ['entradas' => 0.0, 'saidas' => 0.0];
        } else {
            $anterior = self::somarMovimento(null, $inicioMes->copy()->subDay()->toDateString());
            $saldoAnterior = $anterior['entradas'] - $anterior['saidas'];
            $fimMovimento = $corte->lt($fimMes) ? $corte->toDateString() : $fimMes->toDateString();
            $movimentoMes = self::somarMovimento($inicioMes->toDateString(), $fimMovimento);
        }

        $saldoMes = $movimentoMes['entradas'] - $movimentoMes['saidas'];

        return [
            'geral' => $anoSelecionado ? $anos[$anoSelecionado] : self::montarEscopo(0, 0, 0, 0),
            'mes' => self::montarEscopo(
                $saldoAnterior,
                $movimentoMes['entradas'],
                $movimentoMes['saidas'],
                $saldoAnterior + $saldoMes
            ),
            'anos' => $anos,
            'ano' => $anoSelecionado,
            'faixa' => [
                'rotulo' => self::rotuloMes($mesAtual, true),
                'valor' => $mesAtual,
                'entradas' => self::formatarMoeda($movimentoMes['entradas']),
                'saidas' => self::formatarMoeda($movimentoMes['saidas']),
                'saldo' => self::formatarMoeda($saldoMes),
                'saldoClasse' => $saldoMes < 0 ? 'text-danger' : '',
            ],
            'atualizadoAte' => $corte->format('d/m/Y'),
        ];
    }

    /**
     * Formata valor em real. Negativo usa travessão antes do símbolo.
     *
     * @param float $valor
     * @param bool $sinalPositivo
     *
     * @return string
     */
    public static function formatarMoeda(float $valor, bool $sinalPositivo = false): string
    {
        $formatado = number_format(abs($valor), 2, ',', '.');

        if ($valor < 0) {
            return '– R$ ' . $formatado;
        }

        if ($sinalPositivo && $valor > 0) {
            return '+ R$ ' . $formatado;
        }

        return 'R$ ' . $formatado;
    }

    /**
     * Moeda dos PDFs. Negativo usa o sinal de menos; positivo pode levar o sinal de mais.
     *
     * @param float $valor
     * @param bool $sinalPositivo
     *
     * @return string
     */
    public static function formatarValorRelatorio(float $valor, bool $sinalPositivo = false): string
    {
        $formatado = number_format(abs($valor), 2, ',', '.');

        if ($valor < 0) {
            return '− R$ ' . $formatado;
        }

        if ($sinalPositivo && $valor > 0) {
            return '+ R$ ' . $formatado;
        }

        return 'R$ ' . $formatado;
    }

    /**
     * @param string|null $inicio
     * @param string|null $fim
     *
     * @return array{entradas: float, saidas: float}
     */
    private static function somarMovimento(?string $inicio = null, ?string $fim = null): array
    {
        $totais = Lancamento::query()
            ->when($inicio, function ($sql) use ($inicio) {
                return $sql->where('data_lancamento', '>=', $inicio);
            })
            ->when($fim, function ($sql) use ($fim) {
                return $sql->where('data_lancamento', '<=', $fim);
            })
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN tipo = ? THEN valor ELSE 0 END), 0) as entradas,
                COALESCE(SUM(CASE WHEN tipo = ? THEN valor ELSE 0 END), 0) as saidas',
                [Lancamento::TIPO_ENTRADA, Lancamento::TIPO_SAIDA]
            )
            ->first();

        return [
            'entradas' => (float) ($totais->entradas ?? 0),
            'saidas' => (float) ($totais->saidas ?? 0),
        ];
    }

    /**
     * @param float $saldoInicial
     * @param float $entradas
     * @param float $saidas
     * @param float $saldoAtual
     *
     * @return array
     */
    private static function montarEscopo(
        float $saldoInicial,
        float $entradas,
        float $saidas,
        float $saldoAtual
    ): array {
        return [
            'saldoInicial' => self::formatarMoeda($saldoInicial),
            'saldoInicialClasse' => $saldoInicial < 0 ? 'text-danger' : '',
            'entradas' => self::formatarMoeda($entradas),
            'saidas' => self::formatarMoeda($saidas),
            'saldoAtual' => self::formatarMoeda($saldoAtual),
            'saldoAtualClasse' => $saldoAtual < 0 ? 'text-danger' : '',
        ];
    }

    /**
     * Um escopo por ano com lançamento, do mais recente ao mais antigo.
     * O saldo inicial de cada ano é o saldo acumulado dos anos anteriores.
     *
     * @return array<string, array>
     */
    private static function escoposPorAno(): array
    {
        $movimentos = Lancamento::query()
            ->selectRaw(
                'YEAR(data_lancamento) as ano,
                COALESCE(SUM(CASE WHEN tipo = ? THEN valor ELSE 0 END), 0) as entradas,
                COALESCE(SUM(CASE WHEN tipo = ? THEN valor ELSE 0 END), 0) as saidas',
                [Lancamento::TIPO_ENTRADA, Lancamento::TIPO_SAIDA]
            )
            ->groupBy(DB::raw('YEAR(data_lancamento)'))
            ->orderBy('ano')
            ->get();

        $saldo = 0.0;
        $anos = [];

        foreach ($movimentos as $movimento) {
            $entradas = (float) $movimento->entradas;
            $saidas = (float) $movimento->saidas;
            $anos[(string) $movimento->ano] = self::montarEscopo(
                $saldo,
                $entradas,
                $saidas,
                $saldo + $entradas - $saidas
            );
            $saldo += $entradas - $saidas;
        }

        krsort($anos, SORT_NUMERIC);

        return $anos;
    }

    /**
     * @param string $mes
     * @param bool $comBarra
     *
     * @return string
     */
    private static function rotuloMes(string $mes, bool $comBarra = false): string
    {
        $nomes = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];

        $data = Carbon::createFromFormat('!Y-m', $mes);
        $nome = $nomes[(int) $data->format('n')];
        $separador = $comBarra ? '/' : ' ';

        return $nome . $separador . $data->format('Y');
    }

    /**
     * Dados dos relatórios em PDF do ano selecionado.
     *
     * @param string $modelo
     * @param int $ano
     *
     * @return array
     */
    public static function relatorio(string $modelo, int $ano): array
    {
        $cabecalho = self::cabecalhoRelatorio();
        $cabecalho['ano'] = $ano;
        $cabecalho['exercicio'] = sprintf('01/01/%d a 31/12/%d', $ano, $ano);

        if ($modelo === self::MODELO_MOVIMENTOS) {
            return array_merge($cabecalho, self::relatorioMovimentos($ano));
        }

        if ($modelo === self::MODELO_ENTRADA_SAIDA) {
            return array_merge($cabecalho, [
                'relatorio' => self::relatorioEntradaSaida($ano),
            ]);
        }

        return array_merge($cabecalho, [
            'relatorio' => self::relatorioCategorias($ano),
        ]);
    }

    /**
     * @param int $ano
     *
     * @return array{relatorio: array<int, array>, saldo_inicial: float}
     */
    private static function relatorioMovimentos(int $ano): array
    {
        $inicio = Carbon::create($ano, 1, 1)->toDateString();
        $fim = Carbon::create($ano, 12, 31)->toDateString();
        $saldoInicial = self::saldoAntesDoAno($ano);
        $saldo = $saldoInicial;
        $entradas = 0.0;
        $saidas = 0.0;
        $qtdEntradas = 0;
        $qtdSaidas = 0;
        $linhas = [];

        Lancamento::query()
            ->with('categoria')
            ->whereBetween('data_lancamento', [$inicio, $fim])
            ->orderBy('data_lancamento')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->each(function (Lancamento $lancamento) use (&$linhas, &$saldo, &$entradas, &$saidas, &$qtdEntradas, &$qtdSaidas) {
                $bruto = (float) $lancamento->getRawOriginal('valor');
                $entrada = (int) $lancamento->tipo === Lancamento::TIPO_ENTRADA;
                $sinal = $entrada ? $bruto : $bruto * -1;
                $saldo += $sinal;
                $semCategoria = is_null($lancamento->categoria_id) || is_null($lancamento->categoria);

                if ($entrada) {
                    $entradas += $bruto;
                    $qtdEntradas++;
                } else {
                    $saidas += $bruto;
                    $qtdSaidas++;
                }

                $linhas[] = [
                    'data_movimento' => Carbon::parse($lancamento->data_lancamento)->format('d/m/Y'),
                    'entrada' => $entrada,
                    'descricao' => $lancamento->descricao,
                    'categoria' => $semCategoria ? 'Sem categoria' : $lancamento->categoria->nome,
                    'sem_categoria' => $semCategoria,
                    'valor' => self::formatarValorRelatorio($sinal, true),
                    'saldo' => self::formatarValorRelatorio($saldo),
                ];
            });

        return [
            'relatorio' => $linhas,
            'saldo_inicial' => $saldoInicial,
            'entradas' => $entradas,
            'saidas' => $saidas,
            'qtd_entradas' => $qtdEntradas,
            'qtd_saidas' => $qtdSaidas,
            'liquido' => $entradas - $saidas,
            'saldo_final' => $saldo,
        ];
    }

    /**
     * @param int $ano
     *
     * @return array
     */
    private static function relatorioEntradaSaida(int $ano): array
    {
        $saldoInicial = self::saldoAntesDoAno($ano);
        $saldo = $saldoInicial;
        $dados = [];
        $totalReceitas = 0.0;
        $totalDespesas = 0.0;
        $siglas = ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'];
        $nomes = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

        for ($mes = 1; $mes <= 12; $mes++) {
            $inicio = Carbon::create($ano, $mes, 1);
            $movimento = self::somarMovimento(
                $inicio->toDateString(),
                $inicio->copy()->endOfMonth()->toDateString()
            );
            $resultado = $movimento['entradas'] - $movimento['saidas'];
            $saldo += $resultado;
            $totalReceitas += $movimento['entradas'];
            $totalDespesas += $movimento['saidas'];
            $dados[] = [
                'nome' => $nomes[$mes - 1],
                'sigla' => $siglas[$mes - 1],
                'receita' => $movimento['entradas'],
                'despesa' => $movimento['saidas'],
                'resultado' => $resultado,
                'saldo' => $saldo,
                'com_movimento' => $movimento['entradas'] > 0 || $movimento['saidas'] > 0,
            ];
        }

        return [
            'saldo-inicial' => $saldoInicial,
            'dados' => $dados,
            'total-receitas' => $totalReceitas,
            'total-despesas' => $totalDespesas,
            'saldo-final' => $saldo,
        ];
    }

    /**
     * @param int $ano
     *
     * @return array
     */
    private static function relatorioCategorias(int $ano): array
    {
        $inicio = Carbon::create($ano, 1, 1)->toDateString();
        $fim = Carbon::create($ano, 12, 31)->toDateString();
        $grupos = [
            Lancamento::TIPO_ENTRADA => [],
            Lancamento::TIPO_SAIDA => [],
        ];
        $semCategoria = 0;
        $valorSemCategoria = 0.0;

        Lancamento::query()
            ->with('categoria')
            ->whereBetween('data_lancamento', [$inicio, $fim])
            ->get()
            ->each(function (Lancamento $lancamento) use (&$grupos, &$semCategoria, &$valorSemCategoria) {
                $valor = (float) $lancamento->getRawOriginal('valor');
                $sem = is_null($lancamento->categoria_id) || is_null($lancamento->categoria);
                $nome = $sem ? 'Sem categoria' : $lancamento->categoria->nome;
                $tipo = (int) $lancamento->tipo;
                $grupos[$tipo][$nome] = ($grupos[$tipo][$nome] ?? 0) + $valor;

                if ($sem) {
                    $semCategoria++;
                    $valorSemCategoria += $valor;
                }
            });

        $receitas = self::formatarGrupoCategoria($grupos[Lancamento::TIPO_ENTRADA], self::CORES_RECEITA);
        $despesas = self::formatarGrupoCategoria($grupos[Lancamento::TIPO_SAIDA], self::CORES_DESPESA);
        $percentualDespesas = $receitas['total'] > 0
            ? ($despesas['total'] / $receitas['total']) * 100
            : 0;

        return [
            'receitas' => $receitas['linhas'],
            'despesas' => $despesas['linhas'],
            'total_receitas' => $receitas['total'],
            'total_despesas' => $despesas['total'],
            'resultado' => $receitas['total'] - $despesas['total'],
            'percentual_despesas' => $percentualDespesas,
            'sem_categoria' => [
                'quantidade' => $semCategoria,
                'valor' => $valorSemCategoria,
            ],
        ];
    }

    /**
     * @param int $ano
     *
     * @return float
     */
    private static function saldoAntesDoAno(int $ano): float
    {
        $ate = Carbon::create($ano, 1, 1)->subDay()->toDateString();
        $anterior = self::somarMovimento(null, $ate);

        return $anterior['entradas'] - $anterior['saidas'];
    }

    /**
     * @param array<string, float> $grupos
     *
     * @return array{linhas: array<int, array>, total: float}
     */
    private static function formatarGrupoCategoria(array $grupos, array $paleta): array
    {
        ksort($grupos, SORT_NATURAL | SORT_FLAG_CASE);
        $total = array_sum($grupos);
        $linhas = [];
        $indice = 0;

        foreach ($grupos as $categoria => $valor) {
            $semCategoria = $categoria === 'Sem categoria';
            $percentual = $total > 0 ? ($valor / $total) * 100 : 0;
            $cor = $semCategoria
                ? self::COR_SEM_CATEGORIA
                : $paleta[$indice % count($paleta)];

            if (!$semCategoria) {
                $indice++;
            }

            $linhas[] = [
                'categoria' => $categoria,
                'sem_categoria' => $semCategoria,
                'valor' => $valor,
                'porcentagem' => number_format($percentual, 2, ',', '.') . '%',
                'largura' => round($percentual, 2),
                'cor' => $cor,
            ];
        }

        return [
            'linhas' => $linhas,
            'total' => $total,
        ];
    }

    /**
     * @return array{nome: string, tesoureiro: string, presidente: string, emitido_em: string}
     */
    private static function cabecalhoRelatorio(): array
    {
        $instancia = UserService::getInstanciaUsuarioLogado();
        $tipo = auth()->user()->role->name ?? '';
        $tesoureiro = '';
        $presidente = '';

        if ($instancia && isset(DiretoriaService::CLASSES_DIRETORIAS[$tipo])) {
            $classe = DiretoriaService::CLASSES_DIRETORIAS[$tipo];
            $diretoria = $classe::query()
                ->where($tipo . '_id', $instancia->id)
                ->first();

            if ($diretoria) {
                $tesoureiro = $diretoria->tesoureiro ?? '';
                $presidente = $diretoria->presidente ?? '';
            }
        }

        return [
            'nome' => $instancia->nome ?? '',
            'tesoureiro' => $tesoureiro,
            'presidente' => $presidente,
            'emitido_em' => Carbon::now()->format('d/m/Y') . ' às ' . Carbon::now()->format('H:i'),
        ];
    }
}
