<?php

namespace App\Services;

use App\Models\Sugestao;
use Exception;
use Throwable;

class HelpdeskService
{
    public static function storeSugestao(array $request): void
    {
        try {
            Sugestao::create([
                'titulo' => $request['titulo'],
                'descricao' => $request['descricao'],
                'telefone' => $request['telefone'],
                'user_id' => auth()->id()
            ]);
        } catch (Throwable $th) {
            throw new Exception($th->getMessage(), 500);
        }
    }

    public static function enviarNotificacao(array $request): void
    {
        $usuario = auth()->user()->email;
        $mensagem = 'Uma nova sugestão foi cadastrada: ' . PHP_EOL;
        $mensagem .= "Título: {$request['titulo']}" . PHP_EOL;
        $mensagem .= "Descrição: {$request['descricao']}" . PHP_EOL;
        $mensagem .= "Usuário: {$usuario}";
        TelegramService::sendMessage($mensagem);
    }

}
