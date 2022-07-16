<?php

namespace App\Services;

use App\Models\ComprovanteACI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ComprovanteAciService
{
    public static function store(Request $request) : ComprovanteACI
    {
        try {
            $comprovante = ComprovanteACI::updateOrCreate([
                'sinodal_id' => Auth::user()->sinodais->first()->id,
                'ano' => date('Y')
            ], [
                'sinodal_id' => Auth::user()->sinodais->first()->id,
                'ano' => date('Y'),
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
}