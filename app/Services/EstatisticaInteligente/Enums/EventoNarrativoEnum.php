<?php

namespace App\Services\EstatisticaInteligente\Enums;

enum EventoNarrativoEnum: string
{
    case CRESCIMENTO_FORTE = 'crescimento_forte';
    case CRESCIMENTO_MODERADO = 'crescimento_moderado';
    case ESTABILIDADE = 'estabilidade';
    case RETRACAO_LEVE = 'retracao_leve';
    case RETRACAO_FORTE = 'retracao_forte';
    case RECUPERACAO_POS_RETRACAO = 'recuperacao_pos_retracao';
    case CRESCIMENTO_INDETERMINADO = 'crescimento_indeterminado';

    case CONCENTRACAO_FAIXA_JOVEM = 'concentracao_faixa_jovem';
    case CONCENTRACAO_FAIXA_ADULTA = 'concentracao_faixa_adulta';
    case DISTRIBUICAO_EQUILIBRADA = 'distribuicao_equilibrada';
    case RENOVACAO_ALTA = 'renovacao_alta';
    case RENOVACAO_BAIXA = 'renovacao_baixa';

    case ALTA_DIVERSIDADE_ATIVIDADES = 'alta_diversidade_atividades';
    case BAIXA_DIVERSIDADE_ATIVIDADES = 'baixa_diversidade_atividades';
    case CONCENTRACAO_CATEGORIA_ATIVIDADE = 'concentracao_categoria_atividade';
    case AUMENTO_ATIVIDADES = 'aumento_atividades';
    case QUEDA_ATIVIDADES = 'queda_atividades';

    case ACIMA_MEDIANA_PORTE = 'acima_mediana_porte';
    case ABAIXO_MEDIANA_PORTE = 'abaixo_mediana_porte';
    case NA_MEDIANA_PORTE = 'na_mediana_porte';
    case MUDANCA_RELEVANTE_POSICAO = 'mudanca_relevante_posicao';

    case ANOMALIA_DETECTADA = 'anomalia_detectada';
    case QUALIDADE_ALTA = 'qualidade_alta';
    case QUALIDADE_MEDIA = 'qualidade_media';
    case QUALIDADE_BAIXA = 'qualidade_baixa';

    case TENDENCIA_ALTA_CONSISTENTE = 'tendencia_alta_consistente';
    case TENDENCIA_QUEDA_CONSISTENTE = 'tendencia_queda_consistente';
    case TENDENCIA_OSCILANTE = 'tendencia_oscilante';

    public function severidade(): SeveridadeInsightEnum
    {
        return match ($this) {
            self::CRESCIMENTO_FORTE,
            self::CRESCIMENTO_MODERADO,
            self::RECUPERACAO_POS_RETRACAO,
            self::RENOVACAO_ALTA,
            self::ALTA_DIVERSIDADE_ATIVIDADES,
            self::AUMENTO_ATIVIDADES,
            self::ACIMA_MEDIANA_PORTE,
            self::QUALIDADE_ALTA,
            self::TENDENCIA_ALTA_CONSISTENTE => SeveridadeInsightEnum::Positivo,

            self::RETRACAO_FORTE,
            self::TENDENCIA_QUEDA_CONSISTENTE => SeveridadeInsightEnum::Critico,

            self::RETRACAO_LEVE,
            self::RENOVACAO_BAIXA,
            self::BAIXA_DIVERSIDADE_ATIVIDADES,
            self::CONCENTRACAO_CATEGORIA_ATIVIDADE,
            self::QUEDA_ATIVIDADES,
            self::ABAIXO_MEDIANA_PORTE,
            self::MUDANCA_RELEVANTE_POSICAO,
            self::ANOMALIA_DETECTADA,
            self::QUALIDADE_BAIXA => SeveridadeInsightEnum::Atencao,

            default => SeveridadeInsightEnum::Informativo,
        };
    }

    public function categoria(): CategoriaIndicadorEnum
    {
        return match ($this) {
            self::CRESCIMENTO_FORTE,
            self::CRESCIMENTO_MODERADO,
            self::ESTABILIDADE,
            self::RETRACAO_LEVE,
            self::RETRACAO_FORTE,
            self::RECUPERACAO_POS_RETRACAO,
            self::CRESCIMENTO_INDETERMINADO => CategoriaIndicadorEnum::Crescimento,

            self::CONCENTRACAO_FAIXA_JOVEM,
            self::CONCENTRACAO_FAIXA_ADULTA,
            self::DISTRIBUICAO_EQUILIBRADA,
            self::RENOVACAO_ALTA,
            self::RENOVACAO_BAIXA => CategoriaIndicadorEnum::Demografia,

            self::ALTA_DIVERSIDADE_ATIVIDADES,
            self::BAIXA_DIVERSIDADE_ATIVIDADES,
            self::CONCENTRACAO_CATEGORIA_ATIVIDADE,
            self::AUMENTO_ATIVIDADES,
            self::QUEDA_ATIVIDADES => CategoriaIndicadorEnum::Atividades,

            self::ACIMA_MEDIANA_PORTE,
            self::ABAIXO_MEDIANA_PORTE,
            self::NA_MEDIANA_PORTE,
            self::MUDANCA_RELEVANTE_POSICAO => CategoriaIndicadorEnum::Comparativo,

            self::ANOMALIA_DETECTADA => CategoriaIndicadorEnum::Anomalia,

            self::QUALIDADE_ALTA,
            self::QUALIDADE_MEDIA,
            self::QUALIDADE_BAIXA => CategoriaIndicadorEnum::Qualidade,

            self::TENDENCIA_ALTA_CONSISTENTE,
            self::TENDENCIA_QUEDA_CONSISTENTE,
            self::TENDENCIA_OSCILANTE => CategoriaIndicadorEnum::Tendencia,
        };
    }

    public function titulo(): string
    {
        return match ($this) {
            self::CRESCIMENTO_FORTE,
            self::CRESCIMENTO_MODERADO,
            self::ESTABILIDADE,
            self::RETRACAO_LEVE,
            self::RETRACAO_FORTE,
            self::RECUPERACAO_POS_RETRACAO,
            self::CRESCIMENTO_INDETERMINADO => 'Crescimento',
            self::CONCENTRACAO_FAIXA_JOVEM,
            self::CONCENTRACAO_FAIXA_ADULTA,
            self::DISTRIBUICAO_EQUILIBRADA,
            self::RENOVACAO_ALTA,
            self::RENOVACAO_BAIXA => 'Demografia',
            self::ALTA_DIVERSIDADE_ATIVIDADES,
            self::BAIXA_DIVERSIDADE_ATIVIDADES,
            self::CONCENTRACAO_CATEGORIA_ATIVIDADE,
            self::AUMENTO_ATIVIDADES,
            self::QUEDA_ATIVIDADES => 'Atividades',
            self::ACIMA_MEDIANA_PORTE,
            self::ABAIXO_MEDIANA_PORTE,
            self::NA_MEDIANA_PORTE,
            self::MUDANCA_RELEVANTE_POSICAO => 'Comparação',
            self::ANOMALIA_DETECTADA => 'Anomalia',
            self::QUALIDADE_ALTA,
            self::QUALIDADE_MEDIA,
            self::QUALIDADE_BAIXA => 'Qualidade',
            self::TENDENCIA_ALTA_CONSISTENTE,
            self::TENDENCIA_QUEDA_CONSISTENTE,
            self::TENDENCIA_OSCILANTE => 'Tendência',
        };
    }

    public function eventoTextual(): string
    {
        return match ($this) {
            self::RENOVACAO_ALTA => 'alta renovação geracional',
            self::RENOVACAO_BAIXA => 'baixa renovação geracional',
            self::CRESCIMENTO_FORTE => 'crescimento expressivo',
            self::RECUPERACAO_POS_RETRACAO => 'recuperação após retração',
            default => str_replace('_', ' ', $this->value),
        };
    }
}
