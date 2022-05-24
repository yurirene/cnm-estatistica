<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\Atividade;
use App\Models\Sinodal;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class CalendarioService
{   
    public static function getCalendario($request)
    {
        try {
            $usuario = User::find(Auth::id());
            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->start)->addMonth();
            $atividades = $usuario->atividades()
                ->where('start', '>', $start->format('Y-m-d'))
                ->where('start', '<', $end->format('Y-m-d'))
                ->get()
                ->map(function($evento) {
                    return [
                        'id' => $evento->id,
                        'title' => $evento->titulo,
                        'start' => $evento->start->format('Y-m-d'),
                        'end' => $evento->start->format('Y-m-d'),
                        'dt' => $evento->start->format('d/m/Y'),
                        'status' => $evento->status,
                        'color' => Atividade::CORES[$evento->tipo]
                    ];
                });
            return $atividades;
        } catch (Throwable $th) {
            return $th->getMessage();
        }
    }

}