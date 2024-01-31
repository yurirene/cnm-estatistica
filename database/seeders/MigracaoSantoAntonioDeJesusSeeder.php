<?php

namespace Database\Seeders;

use App\Models\Local;
use Illuminate\Database\Seeder;

class MigracaoSantoAntonioDeJesusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ump = Local::find('246a77c0-96d4-4823-8d6d-d67d4b9d8e35');
        $ump->update([
            'federacao_id' => '0bfbd60d-9696-48cd-918a-984f70f53af7'
        ]);

    }
}
