<?php

namespace Database\Seeders;

use App\Models\Apps\Site\ModeloSite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModeloSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modelos = [
            [
                'id' => 1,
                'nome' => 'Modelo 1',
                'name' => 'modelo_1',
                'configuracoes' => [
                    'editaveis' => [
                        ['titulo' => 'Meu Título'],
                        ['destaque' => 'Texto Destaque'],
                        ['subtitulo' => 'Meu Subtitulo'],
                        ['linkTitulo' => 'Link Título'],
                        ['link' => 'https://google.com'],
                        ['sobreNos' => '<p>Sobre nós - <i>ASDAS</i></p>'],
                        ['galeria' => []],
                        ['diretoria' => [
                            0 => [
                                'titulo' => 'Presidente',
                                'cargo' => 'presidente',
                                'path' => '',
                                'nome' => 'Nome'
                            ],
                            1 => [
                                'cargo' => 'vice_presidente',
                                'titulo' => 'Vice-Presidente',
                                'path' => '',
                                'nome' => 'Nome'
                            ],
                            2 => [
                                'cargo' => 'secretaria_executiva',
                                'titulo' => 'Secretário-Executivo',
                                'path' => '',
                                'nome' => 'Nome'
                            ],
                            3 => [
                                'cargo' => 'primeiro_secretario',
                                'titulo' => '1º Secretário(a)',
                                'path' => '',
                                'nome' => 'Nome'
                            ],
                            4 => [
                                'cargo' => 'segundo_secretario',
                                'titulo' => '2º Secretário(a)',
                                'path' => '',
                                'nome' => 'Nome'
                            ],
                            5 => [
                                'cargo' => 'tesoureiro',
                                'titulo' => 'Tesoureiro',
                                'path' => '',
                                'nome' => 'Nome'
                            ]
                        ]]
                    ],
                    'nomeSinodal' => 'CSM Sinodal',
                    'federacoes' => [],
                    'totalizador' => [
                        'federacao' => 0,
                        'umps' => 0,
                        'socios' => 0
                    ],
                ],
                'mapeamento' => [
                    'campos' => [
                        'titulo' => 'text',
                        'destaque' => 'text',
                        'subtitulo' => 'text',
                        'linkTitulo' => 'text',
                        'link' => 'text',
                        'sobreNos' => 'rich',
                        'galeria' => 'custom',
                        'diretoria' => 'custom'
                    ],
                    'titulo' => [
                        'titulo' => 'Título',
                        'link' => 'Link Externo',
                        'destaque' => 'Texto em Destaque',
                        'subtitulo' => 'Subtítulo',
                        'linkTitulo' => 'Botão Link Texto',
                        'sobreNos' => 'Sobre Nós',
                        'galeria' => 'Galeria',
                        'diretoria' => 'Diretoria'
                    ]
                ]

            ],
            [
                'id' => 2,
                'nome' => 'Modelo 2',
                'name' => 'modelo_2',
                'configuracoes' => [],
                'mapeamento' => []
            ],
            [
                'id' => 3,
                'nome' => 'Modelo 3',
                'name' => 'modelo_3',
                'configuracoes' => [],
                'mapeamento' => []
            ]
        ];

        DB::beginTransaction();
        try {

            foreach ($modelos as $modelo) {
                ModeloSite::updateOrCreate([
                    'id' => $modelo['id']
                ], $modelo);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            throw $th;
        }
    }
}
