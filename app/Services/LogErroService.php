<?php

namespace App\Services;

use App\Models\LogErro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class LogErroService
{
    
    public static function registrar(array $informacoes) 
    {
        try {
            Log::error($informacoes);
            
            if (env('APP_ENV') == 'local') {
                return;
            }
            LogErro::create([
                'user_id' => Auth::id(),
                'log' => $informacoes
            ]);
            

            self::sendTelegram($informacoes);

        } catch (Throwable $th) {
            Log::error([
                'title' => 'ERRO AO REGISTRAR LOG DE ERRO',
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public static function sendTelegram(array $informacoes)
    {
        try {
            $mensagem = '';
            $mensagem .= 'ERRO NO iCLAUDIA ' . PHP_EOL . PHP_EOL;
            $mensagem .= date('d/m/y h:i:s') . PHP_EOL;
            $mensagem .= 'Usuário: ' . Auth::user()->name ?? 'Não encontrado' . PHP_EOL;
            foreach ($informacoes as $campo => $info) {
                $mensagem .= ucfirst($campo) . ': ' . $info . PHP_EOL;
            }
            
            TelegramService::sendMessage($mensagem);

        } catch (\Throwable $th) {
            Log::error([
                'title' => 'ERRO AO REGISTRAR LOG DE ERRO PELO TELEGRAM',
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }
    
    
}