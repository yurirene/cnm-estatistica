<?php

namespace App\Http\Controllers;

use App\Services\MapaService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $dataMapaBrazil = MapaService::getDefaultMap();

        return view('dashboard.index', [
            'dataMapaBrazil' => $dataMapaBrazil
        ]);
    }
}
