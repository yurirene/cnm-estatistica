<?php

namespace Database\Seeders;

use App\Models\Perfil;
use Illuminate\Database\Seeder;

class PerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $perfis = [
            'cnm' => 'Diretoria CNM',
            'secretario' => 'Secretários de Atividades',
            'sinodal' => 'Sinodal', 
            'federacao' => 'Federação',
            'local' => 'UMP Local'
        ];

        try {
            foreach ($perfis as $key => $perfil) {
                Perfil::create([
                    'nome'=> $key,
                    'descricao' => $perfil
                ]);
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
