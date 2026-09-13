<?php

namespace App\Services\EstatisticaInteligente;

use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use App\Services\EstatisticaInteligente\Enums\NivelEstatisticoEnum;

final class AlvoAnaliseResolver
{
    /**
     * @return array<int, array{nivel: NivelEstatisticoEnum, id: string}>
     */
    public function aposLocal(Local $local): array
    {
        $alvos = [
            ['nivel' => NivelEstatisticoEnum::Local, 'id' => (string) $local->id],
        ];

        if ($local->federacao_id) {
            $alvos[] = ['nivel' => NivelEstatisticoEnum::Federacao, 'id' => (string) $local->federacao_id];
        }

        $sinodalId = $local->sinodal_id ?: $local->federacao?->sinodal_id;
        if ($sinodalId) {
            $alvos[] = ['nivel' => NivelEstatisticoEnum::Sinodal, 'id' => (string) $sinodalId];
        }

        if ($local->regiao_id) {
            $alvos[] = ['nivel' => NivelEstatisticoEnum::Regiao, 'id' => (string) $local->regiao_id];
        }

        $alvos[] = ['nivel' => NivelEstatisticoEnum::Nacional, 'id' => ''];

        return $alvos;
    }

    /**
     * @return array<int, array{nivel: NivelEstatisticoEnum, id: string}>
     */
    public function aposFederacao(Federacao $federacao): array
    {
        $alvos = [
            ['nivel' => NivelEstatisticoEnum::Federacao, 'id' => (string) $federacao->id],
        ];

        if ($federacao->sinodal_id) {
            $alvos[] = ['nivel' => NivelEstatisticoEnum::Sinodal, 'id' => (string) $federacao->sinodal_id];
        }

        if ($federacao->regiao_id) {
            $alvos[] = ['nivel' => NivelEstatisticoEnum::Regiao, 'id' => (string) $federacao->regiao_id];
        }

        $alvos[] = ['nivel' => NivelEstatisticoEnum::Nacional, 'id' => ''];

        return $alvos;
    }

    /**
     * @return array<int, array{nivel: NivelEstatisticoEnum, id: string}>
     */
    public function aposSinodal(Sinodal $sinodal): array
    {
        $alvos = [
            ['nivel' => NivelEstatisticoEnum::Sinodal, 'id' => (string) $sinodal->id],
        ];

        if ($sinodal->regiao_id) {
            $alvos[] = ['nivel' => NivelEstatisticoEnum::Regiao, 'id' => (string) $sinodal->regiao_id];
        }

        $alvos[] = ['nivel' => NivelEstatisticoEnum::Nacional, 'id' => ''];

        return $alvos;
    }
}
