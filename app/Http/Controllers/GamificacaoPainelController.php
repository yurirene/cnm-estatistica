<?php

namespace App\Http\Controllers;

use App\Helpers\DashboardHelper;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class GamificacaoPainelController extends Controller
{
    public function index(): View
    {
        abort_unless(Gate::any(['sinodal', 'federacao']), 403);

        return view('dashboard.gamificacao.painel', [
            'game' => DashboardHelper::getGamificacao(),
        ]);
    }
}
