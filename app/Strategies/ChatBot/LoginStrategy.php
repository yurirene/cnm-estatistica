<?php 

namespace App\Strategies\ChatBot;

use App\Factories\MessageFactory;
use App\Interfaces\ChatBotStrategy;
use App\Models\BotCliente;
use App\Models\BotMessage;
use App\Models\User;
use App\Services\IClaudiaService;
use Illuminate\Support\Facades\Log;

class LoginStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        try {
            $message = BotMessage::whereIdentificador('login')->first();
            IClaudiaService::sendMessage($cliente, $message);
        }  catch (\Throwable $th) {
            Log::error([
                'message' => $th->getMessage(), 
                'linha' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }


    public static function processReply(BotCliente $cliente, string $mensagem)
    {
        try {
            $message = BotMessage::whereIdentificador('login')->first();
            $partes = explode(' ', $mensagem);
            $email = null;
            foreach ($partes as $parte) {
                if (filter_var($parte,FILTER_VALIDATE_EMAIL)) {
                    $email = $parte;
                }
            }
            if (is_null($email)) {
                self::refazerLogin($cliente, $mensagem);
            }

            $usuario = User::where('email', $email)->first();

            if (is_null($usuario)) {
                self::refazerLogin($cliente, $mensagem);
            }

            $cliente->update([
                'email' => $email,
                'user_id' => $usuario->id
            ]);

            app()->make(MessageFactory::class)->makeMessage('LoginSucesso')->process($cliente, $mensagem);
            app()->make(MessageFactory::class)->makeMessage('InformacoesUsuario')->process($cliente, $mensagem);
            app()->make(MessageFactory::class)->makeMessage('ListaOpcoes')->process($cliente, $mensagem);

        }  catch (\Throwable $th) {
            Log::error([
                'message' => $th->getMessage(), 
                'linha' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public static function refazerLogin(BotCliente $cliente, string $mensagem_usuario)
    {
        app()->make(MessageFactory::class)->makeMessage('LoginErro')->process($cliente, $mensagem_usuario);
        app()->make(MessageFactory::class)->makeMessage('Login')->process($cliente, $mensagem_usuario);
    }

}