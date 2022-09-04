<?php

namespace App\Services;

use App\Exports\BaseDadosFormularioExport;
use App\Models\FormularioSinodal;
use App\Models\Parametro;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EstatisticaService
{

    public static function atualizarParametro(array $request) : void
    {
        $valor = $request['valor'];
        if (in_array($valor,['true', 'false'])) {
            $valor = $request['valor'] == 'true' ? 'SIM' : 'NAO';
        }
        try {
            $parametro = Parametro::find($request['id']);
            $parametro->update([
                'valor' => $valor
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getParametros() : Collection
    {
        try {
            return Parametro::where('area', 'estatistica')->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'nome' => $item->nome,
                    'valor' => $item->valor,
                    'label' => $item->descricao,
                    'tipo' => $item->tipo
                ];
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getAnoReferenciaFormularios() : array
    {
        return FormularioSinodal::selectRaw('DISTINCT(ano_referencia) as ano_referencia')
        ->groupBy('ano_referencia')
        ->get()
        ->pluck('ano_referencia', 'ano_referencia')
        ->toArray();
    }

    public static function exportarExcel(array $request)
    {
        $formulario_base = FormularioSinodal::with('sinodal')->where('ano_referencia', $request['ano_referencia'])->first()->toArray();
        $dados = collect($formulario_base)->except('id', 'created_at', 'updated_at', 'sinodal', 'deleted_at', 'sinodal_id');
        $campos = [];
        $somente_colunas = [];
        foreach ($dados as $coluna_master => $coluna) {
            if (!is_array($coluna)) {
                $campos[] = $coluna_master;
                $somente_colunas[] = $coluna_master;
                continue;
            }
            $campos[$coluna_master] = array_keys($coluna);
            array_push($somente_colunas, ...array_keys($coluna));
        }
        return Excel::download(new BaseDadosFormularioExport($campos, $somente_colunas, $request['ano_referencia']), 'base_dados_' . date('d_m_Y') . '.xlsx');

    }

}
