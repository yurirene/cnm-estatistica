<?php

namespace App\Http\Controllers\Apps;

use App\DataTables\TesourariaLancamentosDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TesourariaController extends Controller
{
    public function index(TesourariaLancamentosDataTable $dataTable)
    {
        return $dataTable->render('dashboard.apps.tesouraria.index');
    }
}
