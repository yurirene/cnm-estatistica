<?php

namespace App\Services;

use App\Models\ComprovanteACI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'ano' => date('Y')
            ]);

            if (!is_null($comprovante->path)) {
                unlink(storage_path('public/comprovante_aci/'.$comprovante->path));
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
}