<?php

namespace Database\Seeders;

use App\Models\Apps\Site\ModeloSite;
use App\Models\Apps\Site\Site;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteAtualizarConfigSeeder extends Seeder
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
            $modelos = ModeloSite::all();
            foreach ($modelos as $modeloModel) {
                $sites = Site::where('modelo_id', $modeloModel->id)->get();
                $modelo = $modeloModel->configuracoes;
                foreach ($sites as $site) {
                    $atual = $site->configuracoes;
                    foreach ($modelo as $chave1 => $config) {
                        if (!is_array($config)) {
                            dd('não é array', $config);
                        }
                        foreach ($config as $chave2 => $conf) {

                            if (!is_array($conf)) {
                                if (!isset($atual[$chave1][$chave2])) {
                                    $atual[$chave1][$chave2] = $modelo[$chave1][$chave2];
                                }
                                continue;
                            }

                            foreach ($conf as $chave3 => $c) {
                                if (!is_array($c)) {
                                    continue;
                                }
                                if (isset($atual[$chave1][$chave2][$chave3])) {
                                    if (count($atual[$chave1][$chave2][$chave3]) >= count($modelo[$chave1][$chave2][$chave3])) {
                                        continue;
                                    }
                                    $atual[$chave1][$chave2][$chave3] = $atual[$chave1][$chave2][$chave3] + $modelo[$chave1][$chave2][$chave3];
                                } else {
                                    $atual[$chave1][$chave2][$chave3] = $modelo[$chave1][$chave2][$chave3];
                                }

                            }
                        }
                    }
                }
                $site->configuracoes = $atual;
                $site->save();
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }

    }
}
