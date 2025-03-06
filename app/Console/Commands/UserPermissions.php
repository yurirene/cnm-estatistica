<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Roda a seeder de permissoes e limpa o cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('db:seed', ['--class' => 'AcessoSeeder']);
        Artisan::call('cache:clear');
        return 0;
    }
}
