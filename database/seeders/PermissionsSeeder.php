<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Yajra\Acl\Models\Permission;

use function PHPSTORM_META\map;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

       try {
            $sufixos = [
                'index' => 'Iniciar',
                'view' => 'Visualizar - V',
                'show' => 'Visualizar - S',
                'create' => 'Criar',
                'store' => 'Cadastrar',
                'edit' => 'Editar',
                'update' => 'Atualizar',
                'datatable' => 'DataTable'
            ];

            $routes = collect(Route::getRoutes())->filter(function ($route) {
                return in_array('web', $route->action['middleware']) && key_exists('modulo', $route->action);
            })
            ->pluck('action')
            ->toArray();
            foreach ($routes as $route) {
                $action = explode('.', $route['as']);
                $sufixo = key_exists(end($action), $sufixos) ? $sufixos[end($action)] : end($action);
                $data = [
                    'name' => 'Módulo ' . ucfirst($route['modulo']) . ' - ' . $sufixo,
                    'slug' => $route['as'],
                    'resource' => $route['modulo'],
                ];
                Permission::firstOrCreate(['slug' => $route['as']], $data);
            }
       } catch (\Throwable $th) {
            dd($th->getMessage(), $th->getLine(), $th->getFile());
       }
    }
}
