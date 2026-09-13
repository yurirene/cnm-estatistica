<?php

namespace App\Services\EstatisticaInteligente\DTOs;

final class ContextoIaDTO
{
    /**
     * @param  array<string, mixed>  $indicadores
     * @param  array<int, array<string, mixed>>  $historico
     * @param  array<string, mixed>|null  $comparativo
     * @param  array<int, mixed>  $anomalias
     * @param  array<string, mixed>|null  $qualidade
     */
    public function __construct(
        public readonly int $periodo,
        public readonly string $nivel = 'local',
        public readonly ?string $organizacaoId = null,
        public readonly array $indicadores = [],
        public readonly array $historico = [],
        public readonly ?array $comparativo = null,
        public readonly array $anomalias = [],
        public readonly ?array $qualidade = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    public static function fromArray(array $dados): self
    {
        $comparativo = $dados['comparativo'] ?? $dados['comparativos'] ?? null;

        return new self(
            periodo: (int) ($dados['periodo'] ?? $dados['ano'] ?? 0),
            nivel: (string) ($dados['nivel'] ?? 'local'),
            organizacaoId: self::valorOpcionalString($dados['organizacao_id'] ?? $dados['organizacaoId'] ?? null),
            indicadores: self::normalizarIndicadores((array) ($dados['indicadores'] ?? [])),
            historico: self::normalizarHistorico((array) ($dados['historico'] ?? [])),
            comparativo: is_array($comparativo) ? $comparativo : null,
            anomalias: array_values((array) ($dados['anomalias'] ?? [])),
            qualidade: isset($dados['qualidade']) && is_array($dados['qualidade']) ? $dados['qualidade'] : null,
        );
    }

    public function indicador(string $codigo): mixed
    {
        if (! array_key_exists($codigo, $this->indicadores)) {
            return null;
        }

        return $this->indicadores[$codigo];
    }

    /**
     * @return array<string, mixed>
     */
    public function historicoAno(int $ano): array
    {
        return $this->historico[$ano] ?? [];
    }

    public function historicoIndicador(int $ano, string $codigo): mixed
    {
        $anoDados = $this->historicoAno($ano);
        $normalizados = self::normalizarIndicadores($anoDados);

        if (! array_key_exists($codigo, $normalizados)) {
            return null;
        }

        return $normalizados[$codigo];
    }

    public function percentil(): ?float
    {
        return self::floatOpcional($this->comparativo['percentil'] ?? null);
    }

    public function percentilAnterior(): ?float
    {
        return self::floatOpcional($this->comparativo['percentil_anterior'] ?? null);
    }

    public function qualidadeScore(): ?float
    {
        return self::floatOpcional($this->qualidade['score'] ?? null);
    }

    public function hash(): string
    {
        $canonico = self::ordenarRecursivo($this->toArray());
        $json = json_encode($canonico, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return hash('sha256', $json === false ? '' : $json);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'periodo' => $this->periodo,
            'nivel' => $this->nivel,
            'organizacao_id' => $this->organizacaoId,
            'indicadores' => $this->indicadores,
            'historico' => $this->historico,
            'comparativo' => $this->comparativo,
            'anomalias' => $this->anomalias,
            'qualidade' => $this->qualidade,
        ];
    }

    /**
     * @param  array<string, mixed>  $indicadores
     * @return array<string, mixed>
     */
    private static function normalizarIndicadores(array $indicadores): array
    {
        $aliases = [
            'crescimento_anual' => 'crescimento_ativos_anual',
            'renovacao_geracional' => 'indice_renovacao_geracional',
        ];

        foreach ($aliases as $de => $para) {
            if (array_key_exists($de, $indicadores) && ! array_key_exists($para, $indicadores)) {
                $indicadores[$para] = $indicadores[$de];
            }
        }

        return $indicadores;
    }

    /**
     * @param  array<mixed>  $historico
     * @return array<int, array<string, mixed>>
     */
    private static function normalizarHistorico(array $historico): array
    {
        $saida = [];

        foreach ($historico as $ano => $dados) {
            if (! is_array($dados)) {
                continue;
            }

            $saida[(int) $ano] = self::normalizarIndicadores($dados);
        }

        ksort($saida);

        return $saida;
    }

    private static function valorOpcionalString(mixed $valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        return (string) $valor;
    }

    private static function floatOpcional(mixed $valor): ?float
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        if (! is_numeric($valor)) {
            return null;
        }

        return (float) $valor;
    }

    /**
     * @param  array<mixed>  $dados
     * @return array<mixed>
     */
    private static function ordenarRecursivo(array $dados): array
    {
        foreach ($dados as $chave => $valor) {
            if (is_array($valor)) {
                $dados[$chave] = self::ordenarRecursivo($valor);
            }
        }

        ksort($dados);

        return $dados;
    }
}
