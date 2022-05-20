<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('estados')->insert([
            ['nome' => 'Acre', 'sigla' => 'AC', 'regiao_id' => 1],
            ['nome' => 'Amapá', 'sigla' => 'AP', 'regiao_id' => 1],
            ['nome' => 'Amazonas', 'sigla' => 'AM', 'regiao_id' => 1],
            ['nome' => 'Pará', 'sigla' => 'PA', 'regiao_id' => 1],
            ['nome' => 'Rondônia', 'sigla' => 'RO', 'regiao_id' => 1],
            ['nome' => 'Roraima', 'sigla' => 'RR', 'regiao_id' => 1],
            ['nome' => 'Tocantins', 'sigla' => 'TO', 'regiao_id' => 1],
            
            ['nome' => 'Alagoas', 'sigla' => 'AL', 'regiao_id' => 2],
            ['nome' => 'Bahia', 'sigla' => 'BA', 'regiao_id' => 2],
            ['nome' => 'Ceará', 'sigla' => 'CE', 'regiao_id' => 2],
            ['nome' => 'Maranhão', 'sigla' => 'MA', 'regiao_id' => 2],
            ['nome' => 'Paraíba', 'sigla' => 'PB', 'regiao_id' => 2],
            ['nome' => 'Pernambuco', 'sigla' => 'PE', 'regiao_id' => 2],
            ['nome' => 'Piauí', 'sigla' => 'PI', 'regiao_id' => 2],
            ['nome' => 'Sergipe', 'sigla' => 'SE', 'regiao_id' => 2],
            ['nome' => 'Rio Grande do Norte', 'sigla' => 'RN', 'regiao_id' => 2],
            
            ['nome' => 'Goiás', 'sigla' => 'GO', 'regiao_id' => 3],
            ['nome' => 'Mato Grosso', 'sigla' => 'MT', 'regiao_id' => 3],
            ['nome' => 'Mato Grosso do Sul', 'sigla' => 'MS', 'regiao_id' => 3],
            ['nome' => 'Distrito Federal', 'sigla' => 'DF', 'regiao_id' => 3],
            
            ['nome' => 'Rio de Janeiro', 'sigla' => 'RJ', 'regiao_id' => 4],
            ['nome' => 'Espírito Santo', 'sigla' => 'ES', 'regiao_id' => 4],
            ['nome' => 'São Paulo', 'sigla' => 'SP', 'regiao_id' => 4],
            ['nome' => 'Minas Gerais', 'sigla' => 'MG', 'regiao_id' => 4],
            
            ['nome' => 'Paraná', 'sigla' => 'PR', 'regiao_id' => 5],
            ['nome' => 'Santa Catarina', 'sigla' => 'SC', 'regiao_id' => 5],
            ['nome' => 'Rio Grande do Sul', 'sigla' => 'RS', 'regiao_id' => 5],
        ]);
    }
}
