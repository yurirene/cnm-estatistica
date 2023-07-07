<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioLocalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();
            if (env('APP_ENV') != 'local') {
                return;
            }
            $senha = Hash::make('123');
            $users = User::all();
            foreach ($users as $user) {
                $user->update(['password' => $senha]);
            }
            DB::commit();
            return;
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
        }
    }
}
