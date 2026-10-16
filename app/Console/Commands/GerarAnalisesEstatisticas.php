<?php

namespace App\Console\Commands;

use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\FormularioSinodal;
use App\Models\Regiao;
use App\Services\Estatistica\EstatisticaService;
use App\Services\EstatisticaInteligente\Enums\NivelEstatisticoEnum;
use App\Services\EstatisticaInteligente\GerarAnaliseService;
use Illuminate\Console\Command;

class GerarAnalisesEstatisticas extends Command
{
    protected $signature = 'estatistica:analisar {ano?} {--forcar}';

    protected $description = 'Gera (ou atualiza) as análises comparativas persistidas do ano de referência';

    public function handle(GerarAnaliseService $service): int
    {
        $ano = $this->argument('ano')
            ? (int) $this->argument('ano')
            : EstatisticaService::getAnoReferencia();
        $forcar = (bool) $this->option('forcar');
        $total = 0;

        foreach (FormularioLocal::query()->where('ano_referencia', $ano)->pluck('local_id')->unique() as $id) {
            if ($service->gerar(NivelEstatisticoEnum::Local, (string) $id, $ano, $forcar)) {
                $total++;
            }
        }

        foreach (FormularioFederacao::query()->where('ano_referencia', $ano)->pluck('federacao_id')->unique() as $id) {
            if ($service->gerar(NivelEstatisticoEnum::Federacao, (string) $id, $ano, $forcar)) {
                $total++;
            }
        }

        foreach (FormularioSinodal::query()->where('ano_referencia', $ano)->pluck('sinodal_id')->unique() as $id) {
            if ($service->gerar(NivelEstatisticoEnum::Sinodal, (string) $id, $ano, $forcar)) {
                $total++;
            }
        }

        foreach (Regiao::query()->pluck('id') as $id) {
            if ($service->gerar(NivelEstatisticoEnum::Regiao, (string) $id, $ano, $forcar)) {
                $total++;
            }
        }

        if ($service->gerar(NivelEstatisticoEnum::Nacional, '', $ano, $forcar)) {
            $total++;
        }

        $this->info("Análises geradas/atualizadas: {$total} (ano {$ano})");

        return self::SUCCESS;
    }
}
