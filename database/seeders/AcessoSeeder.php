<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AcessoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('permission_role')->truncate();
            DB::table('permissions')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->call([
                RolesSeeder::class,
                PermissionsSeeder::class,
                PermissionRoleSeeder::class,
            ]);
            Artisan::call('cache:clear');
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
