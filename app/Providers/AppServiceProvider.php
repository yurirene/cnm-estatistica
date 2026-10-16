<?php

namespace App\Providers;

use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GamificacaoConfiguracaoService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        LogViewer::auth(function ($request) {
            return $request->user()
                && in_array($request->user()->email, [
                    'yuri@ump.net.br',
                ]);
        });

        try {
            app(GamificacaoConfiguracaoService::class)->aplicar();
        } catch (\Throwable) {
        }
    }
}
