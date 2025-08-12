<?php

namespace App\Providers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function (User $user) {
            return $user->admin == true;
        });

        Gate::define('diretoria', function (User $user) {
            return 'diretoria' == $user->role->name;
        });

        Gate::define('secretario', function (User $user) {
            return 'secretario' == $user->role->name;
        });

        Gate::define('sinodal', function (User $user) {
            return 'sinodal' == $user->role->name;
        });

        Gate::define('federacao', function (User $user) {
            return 'federacao' == $user->role->name;
        });

        Gate::define('local', function (User $user) {
            return 'local' == $user->role->name;
        });

        Gate::define('permitido', function(User $user, ...$perfis) {
            foreach ($perfis as $perfil) {
                if ($perfil == $user->role->name) {
                    return true;
                }
            }
        });

        Gate::define('apps', function (User $user, ...$apps) {
            if (empty($apps)) {
                $apps = ['sites', 'tesouraria'];
            }

            $instancia = UserService::getInstanciaUsuarioLogado($user);

            if (
                empty($instancia)
                || $user->role->name != User::ROLE_SINODAL
            ) {
                return false;
            }

            $retorno = false;

            foreach ($apps as $app) {
                if ($instancia->apps()->where('name', $app)->get()->isNotEmpty()) {
                    $retorno = true;
                }
            }

            return $retorno;
        });


        Gate::define('rota-permitida', function(User $user, ...$rotas) {
            $permissoes = $user->role
                ->permissions
                ->pluck('slug')
                ->toArray();
            foreach ($rotas as $rota) {
                if (in_array($rota, $permissoes)) {
                    return true;
                }
            }
        });

    }
}
