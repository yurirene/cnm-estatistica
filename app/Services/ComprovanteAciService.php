<?php

namespace App\Services;

use App\Models\ComprovanteACI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ComprovanteAciService
{

    /**
     * Porcentagem a ser passada pela sinodal da ACI para a CNM
     * @var float
     */
    public const PORCENTAGEM_SINODAL = 0.25;
    
    public static function store(Request $request) : ComprovanteACI
    {
        try {
            $comprovante = ComprovanteACI::updateOrCreate([
                'sinodal_id' => auth()->user()->sinodal_id,
                'ano' => $request->ano
            ], [
                'sinodal_id' => auth()->user()->sinodal_id,
                'ano' => $request->ano,
                'status' => false
            ]);

            if (!is_null($comprovante->path)) {
                $real_path = __DIR__ . '/../../storage/app/public';
                $complete_path = str_replace('/storage', $real_path ,$comprovante->path);
                unlink($complete_path);
            }

            if ($request->has('arquivo')) {
                $path = $request->file('arquivo')->store('public/comprovante_aci');
                $comprovante->update([
                    'path' => '/' . str_replace('public', 'storage', $path)
                ]);
            }

            return $comprovante;
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    public static function alterarStatus(ComprovanteACI $comprovante) : void
    {
        try {
            $comprovante->update([
                'status' => !$comprovante->status
            ]);
        } catch (Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    /**
     * Retorna um array contendo os anos de envio do comprovante de aci
     *
     * @return array
     */
    public static function getAnosCadastrados() : array
    {
        return ComprovanteACI::selectRaw('DISTINCT(ano) as ano')
            ->groupBy('ano')
            ->get()
            ->pluck('ano', 'ano')
            ->toArray();
    }
}
