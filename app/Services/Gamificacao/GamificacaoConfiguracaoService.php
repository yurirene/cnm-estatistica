<?php

namespace App\Services\Gamificacao;

use App\Exceptions\GamificacaoException;
use App\Models\Parametro;
use App\Services\Gamificacao\Enums\Pilar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class GamificacaoConfiguracaoService
{
    public const AREA = 'gamificacao';

    private const CACHE_KEY = 'gamificacao.config.overrides';

    /** @var array<string, mixed>|null */
    private ?array $resolvido = null;

    /** @return array<int, array{grupo: string, descricao: string, campos: array<int, array<string, string>>}> */
    public function catalogo(): array
    {
        return [
            [
                'grupo' => 'Ciclo do PE',
                'descricao' => 'Identificação do ciclo de pontuação do Planejamento Estratégico.',
                'campos' => [
                    ['nome' => 'ciclo', 'label' => 'Identificador do ciclo', 'tipo' => 'text', 'ajuda' => 'Ex.: 2026-2030'],
                    ['nome' => 'ciclo_inicio', 'label' => 'Primeiro ano pontuável', 'tipo' => 'number'],
                    ['nome' => 'ciclo_fim', 'label' => 'Último ano pontuável', 'tipo' => 'number'],
                ],
            ],
            [
                'grupo' => 'Prazos',
                'descricao' => 'Datas-limite no ano seguinte à referência (ex.: estatística 2026 fecha em 01/03/2027).',
                'campos' => [
                    ['nome' => 'prazo_estatistica.mes', 'label' => 'Estatística — mês', 'tipo' => 'number'],
                    ['nome' => 'prazo_estatistica.dia', 'label' => 'Estatística — dia', 'tipo' => 'number'],
                    ['nome' => 'prazo_aci.mes', 'label' => 'ACI — mês', 'tipo' => 'number'],
                    ['nome' => 'prazo_aci.dia', 'label' => 'ACI — dia', 'tipo' => 'number'],
                    ['nome' => 'prazo_speed_run.mes', 'label' => 'Speed Run — mês', 'tipo' => 'number'],
                    ['nome' => 'prazo_speed_run.dia', 'label' => 'Speed Run — dia', 'tipo' => 'number'],
                ],
            ],
            [
                'grupo' => 'Tetos de pontuação',
                'descricao' => 'Máximo de pontos que uma instância pode acumular no ano e no ciclo.',
                'campos' => [
                    ['nome' => 'tetos.sinodal', 'label' => 'Teto anual — Sinodal', 'tipo' => 'number'],
                    ['nome' => 'tetos.federacao', 'label' => 'Teto anual — Federação', 'tipo' => 'number'],
                    ['nome' => 'tetos.ciclo_sinodal', 'label' => 'Teto do ciclo — Sinodal', 'tipo' => 'number'],
                    ['nome' => 'tetos.ciclo_federacao', 'label' => 'Teto do ciclo — Federação', 'tipo' => 'number'],
                ],
            ],
            [
                'grupo' => 'Ligas — Sinodal',
                'descricao' => 'Faixas por quantidade de sócios ativos. A prata e o bronze são calculados a partir destes mínimos.',
                'campos' => [
                    ['nome' => 'ligas.sinodal.ouro.min', 'label' => 'Mínimo Liga Ouro', 'tipo' => 'number'],
                    ['nome' => 'ligas.sinodal.prata.min', 'label' => 'Mínimo Liga Prata', 'tipo' => 'number'],
                ],
            ],
            [
                'grupo' => 'Ligas — Federação',
                'descricao' => 'Faixas por quantidade de sócios ativos. A prata e o bronze são calculados a partir destes mínimos.',
                'campos' => [
                    ['nome' => 'ligas.federacao.ouro.min', 'label' => 'Mínimo Liga Ouro', 'tipo' => 'number'],
                    ['nome' => 'ligas.federacao.prata.min', 'label' => 'Mínimo Liga Prata', 'tipo' => 'number'],
                ],
            ],
            [
                'grupo' => 'Descontos de inscrição',
                'descricao' => 'Pontos de ciclo necessários para 50%, 75% e 100% de desconto.',
                'campos' => [
                    ['nome' => 'descontos.sinodal.50', 'label' => 'Sinodal — 50%', 'tipo' => 'number'],
                    ['nome' => 'descontos.sinodal.75', 'label' => 'Sinodal — 75%', 'tipo' => 'number'],
                    ['nome' => 'descontos.sinodal.100', 'label' => 'Sinodal — 100%', 'tipo' => 'number'],
                    ['nome' => 'descontos.federacao.50', 'label' => 'Federação — 50%', 'tipo' => 'number'],
                    ['nome' => 'descontos.federacao.75', 'label' => 'Federação — 75%', 'tipo' => 'number'],
                    ['nome' => 'descontos.federacao.100', 'label' => 'Federação — 100%', 'tipo' => 'number'],
                ],
            ],
            [
                'grupo' => 'Pilares — pontuação máxima',
                'descricao' => 'Pontuação máxima de cada pilar. Pilares tudo-ou-nada aceitam só 0 ou o máximo.',
                'campos' => [
                    ['nome' => 'pilares.estatistica.max', 'label' => 'Estatística — máximo', 'tipo' => 'number'],
                    ['nome' => 'pilares.estatistica.minimo_percentual', 'label' => 'Estatística — % mínima de entrega', 'tipo' => 'number'],
                    ['nome' => 'pilares.aci.max', 'label' => 'ACI — máximo', 'tipo' => 'number'],
                    ['nome' => 'pilares.aci.minimo_percentual', 'label' => 'ACI — % mínima do previsto', 'tipo' => 'number'],
                    ['nome' => 'pilares.evangelismo.max', 'label' => 'Evangelismo — máximo', 'tipo' => 'number', 'ajuda' => 'Deve coincidir com a faixa de 100%.'],
                    ['nome' => 'pilares.comissao_executiva.max', 'label' => 'Comissão Executiva — máximo', 'tipo' => 'number'],
                    ['nome' => 'pilares.bonus_missionario.max', 'label' => 'Bônus Missionário — máximo', 'tipo' => 'number'],
                    ['nome' => 'pilares.speed_run.max', 'label' => 'Speed Run — máximo', 'tipo' => 'number'],
                    ['nome' => 'pilares.eventos.max', 'label' => 'Eventos — teto', 'tipo' => 'number', 'ajuda' => 'Teto do pilar. PMF Oficial, PMF Parceria e DJP valem 1x no ano; esporádico pode repetir. Padrão: 17 (5+5+5+2).'],
                    ['nome' => 'pilares.resgate.max', 'label' => 'Resgate — máximo', 'tipo' => 'number'],
                    ['nome' => 'pilares.resgate.maximo_percentual_evangelismo', 'label' => 'Resgate — % máxima de evangelismo para valer', 'tipo' => 'number', 'ajuda' => 'Se as UMPs locais atingirem este percentual, o resgate zera. Padrão: 60% (mínimo de Evangelismo).'],
                ],
            ],
            [
                'grupo' => 'Evangelismo — faixas',
                'descricao' => 'Pontos conforme o percentual de UMPs locais com programação registrada.',
                'campos' => [
                    ['nome' => 'pilares.evangelismo.faixas.60', 'label' => '60% das UMPs', 'tipo' => 'number'],
                    ['nome' => 'pilares.evangelismo.faixas.80', 'label' => '80% das UMPs', 'tipo' => 'number'],
                    ['nome' => 'pilares.evangelismo.faixas.100', 'label' => '100% das UMPs', 'tipo' => 'number'],
                ],
            ],
            [
                'grupo' => 'Conquistas (eventos e bônus)',
                'descricao' => 'Pontos concedidos por conquista cadastrada. Bônus e resgate espelham o máximo do pilar correspondente.',
                'campos' => [
                    ['nome' => 'conquistas.bonus_missionario.pontos', 'label' => 'Bônus Missionário', 'tipo' => 'number'],
                    ['nome' => 'conquistas.resgate.pontos', 'label' => 'Resgate', 'tipo' => 'number'],
                    ['nome' => 'conquistas.pmf_oficial.pontos', 'label' => 'PMF Oficial', 'tipo' => 'number'],
                    ['nome' => 'conquistas.pmf_parceria.pontos', 'label' => 'PMF Parceria', 'tipo' => 'number'],
                    ['nome' => 'conquistas.djp.pontos', 'label' => 'DJP', 'tipo' => 'number'],
                    ['nome' => 'conquistas.esporadico.pontos', 'label' => 'Evento esporádico', 'tipo' => 'number'],
                ],
            ],
        ];
    }

    /** @return array<string, array<string, string>> */
    public function camposPorNome(): array
    {
        $mapa = [];
        foreach ($this->catalogo() as $grupo) {
            foreach ($grupo['campos'] as $campo) {
                $mapa[$campo['nome']] = $campo;
            }
        }

        return $mapa;
    }

    public static function get(string $chave, mixed $default = null): mixed
    {
        return app(self::class)->valor($chave, $default);
    }

    public static function getInt(string $chave, int $default = 0): int
    {
        return (int) self::get($chave, $default);
    }

    public function valor(string $chave, mixed $default = null): mixed
    {
        return data_get($this->carregado(), $chave, $default);
    }

    /** @return array<int, array{grupo: string, descricao: string, campos: array<int, array<string, mixed>>}> */
    public function formulario(): array
    {
        $this->aplicar();
        $grupos = $this->catalogo();

        foreach ($grupos as &$grupo) {
            foreach ($grupo['campos'] as &$campo) {
                $campo['valor'] = $this->valor($campo['nome']);
            }
        }

        return $grupos;
    }

    public function aplicar(): void
    {
        $this->resolvido = null;
        $this->carregado();
    }

    /**
     * @param  array<string, mixed>  $valores
     */
    public function hidratarParaTeste(array $valores): void
    {
        $this->resolvido = $this->defaults();
        foreach ($valores as $nome => $valor) {
            data_set($this->resolvido, $nome, $valor);
        }
        $this->aplicarDerivados();
    }

    /**
     * @param  array<string, mixed>  $valores
     */
    public function atualizar(array $valores): void
    {
        $permitidos = $this->camposPorNome();

        foreach ($valores as $nome => $valor) {
            if (! is_string($nome)) {
                continue;
            }
            $nome = str_replace('__', '.', $nome);
            if (! isset($permitidos[$nome])) {
                continue;
            }

            $campo = $permitidos[$nome];
            $normalizado = $this->normalizar($campo, $valor);

            Parametro::updateOrCreate(
                ['nome' => $nome, 'area' => self::AREA],
                [
                    'descricao' => $campo['label'],
                    'valor' => (string) $normalizado,
                    'tipo' => $campo['tipo'],
                ]
            );

            $this->espelharPilarEConquista($nome, $normalizado);
        }

        Cache::forget(self::CACHE_KEY);
        $this->aplicar();
        $this->validarLigas();
    }

    public function semearParametros(): void
    {
        foreach ($this->camposPorNome() as $nome => $campo) {
            $valor = data_get($this->defaults(), $nome);
            Parametro::firstOrCreate(
                ['nome' => $nome, 'area' => self::AREA],
                [
                    'descricao' => $campo['label'],
                    'valor' => is_array($valor) ? json_encode($valor) : (string) $valor,
                    'tipo' => $campo['tipo'],
                ]
            );
        }

        Cache::forget(self::CACHE_KEY);
        $this->resolvido = null;
    }

    /** @return array<string, mixed> */
    private function carregado(): array
    {
        if ($this->resolvido !== null) {
            return $this->resolvido;
        }

        $this->resolvido = $this->defaults();

        if (! app()->runningUnitTests() && $this->tabelaDisponivel()) {
            foreach ($this->overrides() as $nome => $valorBruto) {
                $campo = $this->camposPorNome()[$nome] ?? null;
                if ($campo === null) {
                    continue;
                }
                data_set($this->resolvido, $nome, $this->cast($campo, $valorBruto));
            }
        }

        $this->aplicarDerivados();

        return $this->resolvido;
    }

    /** @return array<string, mixed> */
    private function defaults(): array
    {
        /** @var array<string, mixed> $arquivo */
        $arquivo = require config_path('gamificacao.php');

        return $arquivo;
    }

    private function aplicarDerivados(): void
    {
        $inicio = (int) data_get($this->resolvido, 'ciclo_inicio', 2026);
        $fim = (int) data_get($this->resolvido, 'ciclo_fim', 2029);
        data_set($this->resolvido, 'anos_ciclo', max(1, $fim - $inicio + 1));

        foreach (['sinodal', 'federacao'] as $natureza) {
            $ouroMin = (int) data_get($this->resolvido, "ligas.{$natureza}.ouro.min", 0);
            $prataMin = (int) data_get($this->resolvido, "ligas.{$natureza}.prata.min", 0);
            $prataMax = max(0, $ouroMin - 1);
            $bronzeMax = max(0, $prataMin - 1);

            data_set($this->resolvido, "ligas.{$natureza}.prata.max", $prataMax);
            data_set($this->resolvido, "ligas.{$natureza}.bronze.max", $bronzeMax);
            data_set($this->resolvido, "ligas.{$natureza}.ouro.faixa", $ouroMin . '+ sócios ativos');
            data_set($this->resolvido, "ligas.{$natureza}.prata.faixa", "faixa {$prataMin}–{$prataMax}");
            data_set($this->resolvido, "ligas.{$natureza}.bronze.faixa", "até {$bronzeMax} sócios ativos");
        }

        $faixasEvangelismo = data_get($this->resolvido, 'pilares.evangelismo.faixas', []);
        $pontosFaixas = array_map('intval', array_values(is_array($faixasEvangelismo) ? $faixasEvangelismo : []));
        if ($pontosFaixas !== []) {
            data_set($this->resolvido, 'pilares.evangelismo.max', max($pontosFaixas));
        }

        foreach (Pilar::cases() as $pilar) {
            $max = (int) data_get($this->resolvido, "pilares.{$pilar->value}.max", 0);
            $legais = match ($pilar) {
                Pilar::Eventos => range(0, max(0, $max)),
                Pilar::Evangelismo => $this->legaisEvangelismo($pontosFaixas),
                default => [0, $max],
            };
            data_set($this->resolvido, "pilares.{$pilar->value}.legais", $legais);
        }
    }

    /** @return array<string, string> */
    private function overrides(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                return Parametro::query()
                    ->where('area', self::AREA)
                    ->pluck('valor', 'nome')
                    ->all();
            });
        } catch (\Throwable) {
            return [];
        }
    }

    private function tabelaDisponivel(): bool
    {
        try {
            return Schema::hasTable('parametros');
        } catch (\Throwable) {
            return false;
        }
    }

    /** @param  array<string, string>  $campo */
    private function normalizar(array $campo, mixed $valor): int|string
    {
        if ($campo['tipo'] === 'number') {
            if (! is_numeric($valor)) {
                throw new GamificacaoException("O campo {$campo['label']} deve ser numérico.");
            }

            $numero = (int) $valor;
            if ($numero < 0) {
                throw new GamificacaoException("O campo {$campo['label']} não pode ser negativo.");
            }

            if (str_contains($campo['nome'], '.mes') && ($numero < 1 || $numero > 12)) {
                throw new GamificacaoException("O mês de {$campo['label']} deve estar entre 1 e 12.");
            }

            if (str_contains($campo['nome'], '.dia') && ($numero < 1 || $numero > 31)) {
                throw new GamificacaoException("O dia de {$campo['label']} deve estar entre 1 e 31.");
            }

            if (
                str_starts_with($campo['nome'], 'tetos.')
                && ! str_contains($campo['nome'], 'ciclo')
                && $numero > 200
            ) {
                throw new GamificacaoException('O teto anual não pode passar de 200 (limite do banco).');
            }

            return $numero;
        }

        $texto = trim((string) $valor);
        if ($texto === '') {
            throw new GamificacaoException("O campo {$campo['label']} não pode ficar vazio.");
        }

        return $texto;
    }

    /** @param  array<string, string>  $campo */
    private function cast(array $campo, mixed $valor): int|string
    {
        return $campo['tipo'] === 'number' ? (int) $valor : (string) $valor;
    }

    private function espelharPilarEConquista(string $nome, int|string $valor): void
    {
        $pares = [
            'pilares.bonus_missionario.max' => 'conquistas.bonus_missionario.pontos',
            'conquistas.bonus_missionario.pontos' => 'pilares.bonus_missionario.max',
            'pilares.resgate.max' => 'conquistas.resgate.pontos',
            'conquistas.resgate.pontos' => 'pilares.resgate.max',
        ];

        if (! isset($pares[$nome]) || ! is_numeric($valor)) {
            return;
        }

        $espelho = $pares[$nome];
        $campo = $this->camposPorNome()[$espelho] ?? null;
        if ($campo === null) {
            return;
        }

        Parametro::updateOrCreate(
            ['nome' => $espelho, 'area' => self::AREA],
            [
                'descricao' => $campo['label'],
                'valor' => (string) $valor,
                'tipo' => $campo['tipo'],
            ]
        );
    }

    private function validarLigas(): void
    {
        foreach (['sinodal' => 'Sinodal', 'federacao' => 'Federação'] as $natureza => $rotulo) {
            $ouro = (int) $this->valor("ligas.{$natureza}.ouro.min");
            $prata = (int) $this->valor("ligas.{$natureza}.prata.min");
            if ($ouro <= $prata) {
                throw new GamificacaoException(
                    "Na {$rotulo}, o mínimo da Liga Ouro deve ser maior que o da Liga Prata."
                );
            }
        }
    }

    /** @param  int[]  $pontosFaixas */
    private function legaisEvangelismo(array $pontosFaixas): array
    {
        $legais = array_values(array_unique(array_merge([0], $pontosFaixas)));
        sort($legais);

        return $legais;
    }
}
