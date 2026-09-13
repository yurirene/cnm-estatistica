<?php

namespace App\Services\EstatisticaInteligente\Regras;

use App\Services\EstatisticaInteligente\Contratos\InsightRuleInterface;
use App\Services\EstatisticaInteligente\DTOs\ContextoIaDTO;
use App\Services\EstatisticaInteligente\DTOs\EventoNarrativoDTO;
use App\Services\EstatisticaInteligente\Enums\CategoriaIndicadorEnum;
use App\Services\EstatisticaInteligente\Enums\EventoNarrativoEnum;

final class AtividadesInsightRule implements InsightRuleInterface
{
    private const PERCENTUAIS_CATEGORIA = [
        'percentual_atividade_espiritual',
        'percentual_atividade_evangelistica',
        'percentual_atividade_social',
        'percentual_atividade_recreativa',
        'percentual_atividade_oracao',
    ];

    public function __construct(private readonly FaixasNarrativas $faixas)
    {
    }

    public function categoria(): CategoriaIndicadorEnum
    {
        return CategoriaIndicadorEnum::Atividades;
    }

    public function avaliar(ContextoIaDTO $contexto): array
    {
        $eventos = [];
        $variaveis = [
            'indicador_nome' => 'atividades',
            'ano_atual' => $contexto->periodo,
            'ano_anterior' => $contexto->periodo > 0 ? $contexto->periodo - 1 : $contexto->periodo,
        ];

        $diversidade = $contexto->indicador('diversidade_atividades');
        if (is_numeric($diversidade)) {
            $categorias = (int) $diversidade;
            $variaveisDiversidade = array_merge($variaveis, ['categorias' => $categorias]);

            if ($categorias >= $this->faixas->diversidadeAlta) {
                $eventos[] = EventoNarrativoDTO::de(
                    EventoNarrativoEnum::ALTA_DIVERSIDADE_ATIVIDADES,
                    $variaveisDiversidade
                );
            } elseif ($categorias <= $this->faixas->diversidadeBaixa) {
                $eventos[] = EventoNarrativoDTO::de(
                    EventoNarrativoEnum::BAIXA_DIVERSIDADE_ATIVIDADES,
                    $variaveisDiversidade
                );
            }
        }

        $concentracao = $this->concentracaoCategoria($contexto);
        if ($concentracao !== null && $concentracao >= $this->faixas->concentracaoCategoria) {
            $eventos[] = EventoNarrativoDTO::de(EventoNarrativoEnum::CONCENTRACAO_CATEGORIA_ATIVIDADE, array_merge($variaveis, [
                'percentual' => $concentracao,
            ]));
        }

        $variacao = $this->variacaoTotal($contexto);
        if ($variacao !== null) {
            $variaveisVariacao = array_merge($variaveis, [
                'percentual' => abs($variacao),
                'variacao' => $variacao,
            ]);

            if ($variacao >= $this->faixas->variacaoAtividades) {
                $eventos[] = EventoNarrativoDTO::de(EventoNarrativoEnum::AUMENTO_ATIVIDADES, $variaveisVariacao);
            } elseif ($variacao <= -$this->faixas->variacaoAtividades) {
                $eventos[] = EventoNarrativoDTO::de(EventoNarrativoEnum::QUEDA_ATIVIDADES, $variaveisVariacao);
            }
        }

        return $eventos;
    }

    private function concentracaoCategoria(ContextoIaDTO $contexto): ?float
    {
        $direto = $contexto->indicador('concentracao_categoria_percentual');
        if (is_numeric($direto)) {
            return (float) $direto;
        }

        $valores = [];
        foreach (self::PERCENTUAIS_CATEGORIA as $codigo) {
            $valor = $contexto->indicador($codigo);
            if (is_numeric($valor)) {
                $valores[] = (float) $valor;
            }
        }

        return $valores === [] ? null : max($valores);
    }

    private function variacaoTotal(ContextoIaDTO $contexto): ?float
    {
        $direto = $contexto->indicador('variacao_atividades_anual');
        if (is_numeric($direto)) {
            return (float) $direto;
        }

        $atual = $contexto->indicador('total_atividades');
        $anterior = $contexto->historicoIndicador($contexto->periodo - 1, 'total_atividades');

        if (! is_numeric($atual) || ! is_numeric($anterior) || (float) $anterior === 0.0) {
            return null;
        }

        return (((float) $atual - (float) $anterior) / (float) $anterior) * 100;
    }
}
