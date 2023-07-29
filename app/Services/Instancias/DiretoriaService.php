<?php

namespace App\Services\Instancias;

use App\Helpers\FormHelper;
use App\Models\Atividade;
use App\Models\Diretorias\DiretoriaSinodal;
use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioLocal;
use App\Models\FormularioSinodal;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Sinodal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DiretoriaService
{
    public const CAMPOS_CARGOS = [
        0 => 'presidente',
        1 => 'vice_presidente',
        2 => 'secretario_executivo',
        3 => 'primeiro_secretario',
        4 => 'segundo_secretario',
        5 => 'tesoureiro',
        6 => 'secretario_sinodal'
    ];
    public const CARGOS = [
        0 => 'Presidente',
        1 => 'Vice-Presidente',
        2 => 'Secretário-Executivo',
        3 => 'Primeiro(a) Secretário(a)',
        4 => 'Segundo(a) Secretário(a)',
        5 => 'Tesoureiro(a)',
        6 => 'Secretário Sinodal'
    ];

    public static function getDiretoria(): array
    {
        $diretoria = DiretoriaSinodal::where('sinodal_id', auth()->user()->sinodais->first()->id)->first();
        if (!is_null($diretoria)) {
            self::criarDiretoria();
        }
        $retorno = [];
        foreach (self::CAMPOS_CARGOS as $key => $campo) {
            $retorno[] = [
                'key' => $key,
                'cargo' => self::CARGOS[$key],
                'nome' => $diretoria->{$campo},
                'contato' => $diretoria->{'contato_' . $campo},
                'foto' => $diretoria->{'path_' . $campo}
            ];
        }
        return $retorno;
    }

    public static function criarDiretoria()
    {
        DiretoriaSinodal::create([
            'sinodal_id' => auth()->user()->sinodais->first()->id
        ]);
    }

}
