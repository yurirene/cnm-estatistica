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
                'name' => 'Lilian',
                'email' => 'teste@teste.com',
                'password' => Hash::make('123')
            ]);

            $usuario->regioes()->sync([1]);
            
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
