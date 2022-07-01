<?php

namespace Database\Seeders;

use App\Models\Atividade;
use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usuario = User::first();
        $usuario->regioes()->sync([1]);

        $sinodal = Sinodal::create([
            'nome' => 'Setentrional',
            'sigla' => 'CSMSET',
            'regiao_id' => 1,
            'status' => true
        ]);
        $federacao = Federacao::create([
            'nome' => 'Federação Amazonas de Mocidade Presbiteriana',
            'sigla' => 'FAMP',
            'status' => false,
            'estado_id' => '2',
            'regiao_id' => 1,
            'sinodal_id' => $sinodal->id
        ]);
        $local = Local::create([
            'nome' => 'IPM',
            'status' => false,
            'estado_id' => '2',
            'regiao_id' => 1,
            'sinodal_id' => $sinodal->id,
            'federacao_id' => $federacao->id
        ]);

        Atividade::create([
            'titulo' => 'Programação DEV',
            'start' => '2022-05-25',
            'observacao' => 'Nada',
            'status' => false,
            'user_id' => $usuario->id
        ]);

    }
}
