<?php

namespace Database\Seeders;

use App\Models\Diretoria;
use App\Models\DiretoriaInformacao;
use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiretoriaInicialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            self::gerarLocal();
            self::gerarFederacao();
            self::gerarSinodal();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
    }

    public static function gerarLocal()
    {
        $locais = Local::whereNull('deleted_at')
            ->where('status', true)
            ->whereDoesntHave('diretoria')
            ->get();
        foreach ($locais as $local) {
            $diretoria = Diretoria::create([
                'presidente' => 'Não Informado',
                'vice_presidente' => 'Não Informado',
                'primeiro_secretario' => 'Não Informado',
                'segundo_secretario' => 'Não Informado',
                'secretario_executivo' => 'Não Informado',
                'tesoureiro' => 'Não Informado',
                'secretario_causas' => 'Não Informado',
                'local_id' => $local->id,
                'ano' => 2024
            ]);
            DiretoriaInformacao::create([
                'diretoria_id' => $diretoria->id
            ]);
        }
    }

    public static function gerarFederacao()
    {
        $federacoes = Federacao::whereNull('deleted_at')
            ->where('status', true)
            ->whereDoesntHave('diretoria')
            ->get();
        foreach ($federacoes as $federacao) {
            $diretoria = Diretoria::create([
                'presidente' => 'Não Informado',
                'vice_presidente' => 'Não Informado',
                'primeiro_secretario' => 'Não Informado',
                'segundo_secretario' => 'Não Informado',
                'secretario_executivo' => 'Não Informado',
                'tesoureiro' => 'Não Informado',
                'secretario_causas' => 'Não Informado',
                'federacao_id' => $federacao->id,
                'ano' => 2024
            ]);
            DiretoriaInformacao::create([
                'diretoria_id' => $diretoria->id
            ]);
        }
    }

    public static function gerarSinodal()
    {
        $sinodais = Sinodal::whereNull('deleted_at')
            ->where('status', true)
            ->whereDoesntHave('diretoria')
            ->get();
        foreach ($sinodais as $sinodal) {
            $diretoria = Diretoria::create([
                'presidente' => 'Não Informado',
                'vice_presidente' => 'Não Informado',
                'primeiro_secretario' => 'Não Informado',
                'segundo_secretario' => 'Não Informado',
                'secretario_executivo' => 'Não Informado',
                'tesoureiro' => 'Não Informado',
                'secretario_causas' => 'Não Informado',
                'sinodal_id' => $sinodal->id,
                'ano' => 2024
            ]);
            DiretoriaInformacao::create([
                'diretoria_id' => $diretoria->id
            ]);
        }
    }
}
