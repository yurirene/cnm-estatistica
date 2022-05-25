<?php

namespace App\Services\Formularios;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\FormularioLocal;
use App\Models\Sinodal;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FormularioLocalService
{

    public static function store(Request $request)
    {
        try {
            FormularioLocal::create([
                'perfil' => $request->perfil,
                'estado_civil' => $request->estado_civil,
                'escolaridade' => $request->escolaridade,
                'deficiencias' => $request->deficiencia,
                'programacoes' => $request->programacoes,
                'aci' => $request->aci,
                'ano_referencia' => date('Y'),
                'local_id' => '643146d0-c691-4976-9175-0554e6914089'
            ]);
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Salvar");
            
        }
    }

    public static function delete(FormularioLocal $formulario)
    {
        try {
            $formulario->delete();
        } catch (\Throwable $th) {
            Log::error([
                'erro' => $th->getMessage(),
                'arquivo' => $th->getFile(),
                'linha' => $th->getLine()
            ]);
            throw new Exception("Erro ao Atualizar");
            
        }
    }
}