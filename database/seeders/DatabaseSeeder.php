<?php

namespace Database\Seeders;

use App\Models\Perfil;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RegiaoSeeder::class,
            EstadoSeeder::class,
            RolesSeeder::class,
            PermissionsSeeder::class,
            PermissionRoleSeeder::class,
            UserSeeder::class,
            ParametrosSeeder::class
        ]);
    }
}
