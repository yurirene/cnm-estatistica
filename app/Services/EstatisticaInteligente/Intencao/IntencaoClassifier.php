<?php

namespace App\Services\EstatisticaInteligente\Intencao;

use App\Services\EstatisticaInteligente\Enums\IntencaoPerguntaEnum;

final class IntencaoClassifier
{
    public function classificar(string $pergunta): IntencaoPerguntaEnum
    {
        $texto = $this->normalizar($pergunta);

        foreach ($this->padroes() as $intencao => $palavras) {
            foreach ($palavras as $palavra) {
                if ($texto !== '' && str_contains($texto, $this->normalizar($palavra))) {
                    return IntencaoPerguntaEnum::from($intencao);
                }
            }
        }

        return IntencaoPerguntaEnum::Desconhecida;
    }

    /**
     * @return array<string, string[]>
     */
    private function padroes(): array
    {
        return [
            IntencaoPerguntaEnum::Historico->value => [
                'evoluimos',
                'evoluímos',
                'historico',
                'histórico',
                'ultimos anos',
                'últimos anos',
                'ultimo ano',
                'último ano',
            ],
            IntencaoPerguntaEnum::Comparativo->value => [
                'comparado com',
                'comparativo',
                'outras umps',
                'mediana',
            ],
            IntencaoPerguntaEnum::Atividades->value => [
                'quantas atividades',
                'atividades',
            ],
            IntencaoPerguntaEnum::Crescimento->value => [
                'cresceu',
                'crescimento',
                'aumentou',
            ],
        ];
    }

    private function normalizar(string $texto): string
    {
        $texto = mb_strtolower($texto);
        $comAcento = ['á', 'à', 'ã', 'â', 'é', 'ê', 'í', 'ó', 'ô', 'õ', 'ú', 'ç'];
        $semAcento = ['a', 'a', 'a', 'a', 'e', 'e', 'i', 'o', 'o', 'o', 'u', 'c'];

        return str_replace($comAcento, $semAcento, $texto);
    }
}
