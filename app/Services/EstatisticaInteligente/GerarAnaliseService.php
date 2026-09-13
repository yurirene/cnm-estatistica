<?php

namespace App\Services\EstatisticaInteligente;

use App\Models\Estatistica\AnaliseEstatistica;
use App\Services\EstatisticaInteligente\Contratos\AnaliseIaInterface;
use App\Services\EstatisticaInteligente\Enums\NivelEstatisticoEnum;
use App\Services\EstatisticaInteligente\Enums\StatusAnaliseEnum;
use App\Services\EstatisticaInteligente\Enums\TipoAnaliseEnum;

final class GerarAnaliseService
{
    public function __construct(
        private readonly MontadorContextoService $montador,
        private readonly AnaliseIaInterface $analise,
    ) {
    }

    public function gerar(
        NivelEstatisticoEnum $nivel,
        string $nivelId,
        int $ano,
        bool $forcar = false,
    ): ?AnaliseEstatistica {
        $contexto = $this->montador->montar($nivel, $nivelId, $ano);
        if ($contexto === null) {
            return null;
        }

        $hash = $contexto->hash();
        $catalogo = (string) config('estatistica.catalogo_version', '1.0');
        $tipo = TipoAnaliseEnum::Diagnostico->value;
        $chaveNivelId = $nivel === NivelEstatisticoEnum::Nacional ? '' : $nivelId;

        $existente = AnaliseEstatistica::query()
            ->where('nivel', $nivel->value)
            ->where('nivel_id', $chaveNivelId)
            ->where('ano_referencia', $ano)
            ->where('tipo', $tipo)
            ->first();

        if (! $forcar && $existente && $this->mesmoContexto($existente, $hash, $catalogo)) {
            return $existente;
        }

        $resultado = $this->analise->analisar('', $contexto->toArray());

        return AnaliseEstatistica::updateOrCreate(
            [
                'nivel' => $nivel->value,
                'nivel_id' => $chaveNivelId,
                'ano_referencia' => $ano,
                'tipo' => $tipo,
            ],
            [
                'titulo' => $resultado->titulo,
                'resumo' => $resultado->resumo,
                'conteudo' => $resultado->toArray(),
                'dados_contexto_json' => $contexto->toArray(),
                'modelo_ia' => $resultado->modeloIa,
                'catalogo_version' => $catalogo,
                'contexto_hash' => $hash,
                'status' => StatusAnaliseEnum::Concluida->value,
                'gerado_em' => now(),
            ]
        );
    }

    public function mesmoContexto(AnaliseEstatistica $existente, string $hash, string $catalogo): bool
    {
        return $existente->contexto_hash === $hash
            && $existente->catalogo_version === $catalogo
            && $existente->status === StatusAnaliseEnum::Concluida->value;
    }
}
