<?php

namespace App\Http\Controllers;

use App\Models\LogErro;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DatatableAjaxController extends Controller
{
    public function logErros()
    {
        $logs = LogErro::select(['log_erros.id', 'log_erros.created_at', 'u.name', 'log'])
            ->join('users as u', 'u.id', 'user_id')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'dia' => Carbon::parse($item->created_at)->format('d/m/y H:i:s'),
                    'erro' => $item->log['message'],
                    'usuario' => $item->name,
                    'erro_completo' => $item->getRawOriginal('log')
                ];
            });

        return datatables()::of($logs)->make();
    }
}
