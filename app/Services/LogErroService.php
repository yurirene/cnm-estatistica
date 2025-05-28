<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class LogErroService
{

    public static function registrar(array $informacoes)
    {
        try {

            Log::notice('Erro Lançado', [
                'user_id' => auth()->id() ?? null,
                'log' => $informacoes
            ]);

            if (env('APP_ENV') == 'local') {
                return;
            }

            self::sendTelegram($informacoes);
        } catch (Throwable $th) {
            Log::error('ERRO AO REGISTRAR LOG DE ERRO', [
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public static function sendTelegram(array $informacoes)
    {
        try {
            $usuarioLogado = auth()->user()->email
                ?? session()->get('ultimo_usuario')
                ?? 'Não encontrado';

            $mensagem = '';
            $mensagem .= 'ERRO NA PLATAFORMA ' . PHP_EOL . PHP_EOL;
            $mensagem .= date('d/m/y h:i:s') . PHP_EOL;
            $mensagem .= 'Usuário: ' . $usuarioLogado . PHP_EOL;
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
