<?php

namespace App\Services\EstatisticaInteligente;

use App\Models\Estatistica\AnaliseEstatistica;
use App\Services\EstatisticaInteligente\Enums\NivelEstatisticoEnum;
use App\Services\EstatisticaInteligente\Enums\StatusAnaliseEnum;
use App\Services\EstatisticaInteligente\Enums\TipoAnaliseEnum;

final class AnaliseConsultaService
{
    /**
     * @return array{
     *     titulo: string,
     *     resumo: string,
     *     conteudo: array<string, mixed>,
     *     ano: int,
     *     ano_anterior: int
     * }|null
     */
    public function buscar(NivelEstatisticoEnum $nivel, string $nivelId, int $ano): ?array
    {
        $chaveNivelId = $nivel === NivelEstatisticoEnum::Nacional ? '' : $nivelId;

        $analise = AnaliseEstatistica::query()
            ->where('nivel', $nivel->value)
            ->where('nivel_id', $chaveNivelId)
            ->where('ano_referencia', $ano)
            ->where('tipo', TipoAnaliseEnum::Diagnostico->value)
            ->where('status', StatusAnaliseEnum::Concluida->value)
            ->first();

        if ($analise === null) {
            return null;
        }

        return [
            'titulo' => $analise->titulo,
            'resumo' => $analise->resumo,
            'conteudo' => (array) $analise->conteudo,
            'ano' => $ano,
            'ano_anterior' => $ano - 1,
        ];
    }
}
