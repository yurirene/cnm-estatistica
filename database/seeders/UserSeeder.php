<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $usuario = User::create([
                'name' => 'Yuri',
                'email' => 'yuri@ump.net.br',
                'password' => Hash::make('123'),
                'admin' => true
            ]);

            $usuario->update([
                'role_id' => 1,
                'regiao_id' => 1
            ]);
            
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
