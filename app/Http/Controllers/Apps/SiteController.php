<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Apps\Site\ModeloSite;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $modelo = ModeloSite::find(1);
        return view('dashboard.apps.sites.index', [
            'modelo' => $modelo
        ]);
    }
}
