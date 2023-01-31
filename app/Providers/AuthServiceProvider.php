<?php

namespace App\Providers;

use App\Models\User;
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

        Gate::define('cnm', function (User $user) {
            return in_array('cnm', $user->perfis->pluck('nome')->toArray());
        });

        Gate::define('secretario', function (User $user) {
            return in_array('secretario', $user->perfis->pluck('nome')->toArray());
        });

        Gate::define('sinodal', function (User $user) {
            return in_array('sinodal', $user->perfis->pluck('nome')->toArray());
        });

        Gate::define('federacao', function (User $user) {
            return in_array('federacao', $user->perfis->pluck('nome')->toArray());
        });

        Gate::define('local', function (User $user) {
            return in_array('local', $user->perfis->pluck('nome')->toArray());
        });

        Gate::define('permitido', function(User $user, ...$perfis) {
            foreach ($perfis as $perfil) {
                if (in_array($perfil, $user->perfis->pluck('nome')->toArray())) {
                    return true;
                }
            }
        });

        Gate::define('apps', function (User $user, string $app) {
            $sinodal = $user->sinodais()->first();
            if (!$sinodal) {
                return false;
            }
            return $sinodal->apps()->where('name', $app)->get()->isNotEmpty();
        });

    }
}
