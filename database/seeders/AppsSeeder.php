<?php

namespace Database\Seeders;

use App\Models\Apps\App;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $apps = [
            [
                'id' => 1,
                'nome' => 'Tesouraria',
                'name' => 'tesouraria'
            ]
        ];

        DB::beginTransaction();
        try {

            foreach ($apps as $app) {
                App::updateOrCreate([
                    'id' => $app['id']
                ], $app);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            throw $th;
        }
    }
}
