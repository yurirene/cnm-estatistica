<?php

namespace App\Services;

use App\Factories\MessageFactory;
use App\Models\BotCliente;
use App\Models\BotEnvios;
use App\Models\BotMessage;
use Illuminate\Support\Facades\Log;

class IClaudiaService
{

    public static function processar(array $request)
    {
        $message = $request['message'];
        $nome = $message['chat']['first_name'];
        $chat_id = $message['chat']['id'];

        if (!isset($message['text'])) {
            return;
        }

        $cliente = BotCliente::firstOrCreate([
            'chat_id' => $chat_id,
            'nome' => $nome
        ]);       

        IClaudiaService::decode($cliente, $message['text']);
    }

    public static function decode(BotCliente $cliente, string $message)
    {

        self::salvarMensagemCliente($cliente, $message);

        $ultima_mensagem_do_servidor = BotEnvios::where('bot_cliente_id', $cliente->id)->whereNotNull('mensagem_servidor')->get()->last();
        if (!$ultima_mensagem_do_servidor) {
            app()->make(MessageFactory::class)->makeMessage('BoasVindas')->process($cliente, $message);
            app()->make(MessageFactory::class)->makeMessage('Login')->process($cliente, $message);
        } else {
            self::getResposta($cliente, $message);
        }
    }

    public static function getResposta(BotCliente $cliente, string $mensagem)
    {
        $ultima_mensagem_do_servidor = $cliente->envios()->whereNotNull('mensagem_servidor')->get()->last()->mensagem->identificador;
        $classe = str_replace('_', '', ucwords($ultima_mensagem_do_servidor, '_'));
        app()->make(MessageFactory::class)->makeMessage($classe)->processReply($cliente, $mensagem);
    }

    public static function sendMessage(BotCliente $cliente, BotMessage $mensagem_servidor, array $params = []) 
    {
        BotEnvios::create([
            'bot_cliente_id' => $cliente->id,
            'mensagem_servidor' => $mensagem_servidor->id,
        ]);
        $texto = $mensagem_servidor->mensagem;
        if (count($params)) {
            $texto = str_replace($params['params'], $params['propriedades'], $texto);
        }       

        $parameters = [
            'chat_id' => $cliente->chat_id, 
            "text" => $texto,
            "parse_mode" => "html"
        ];

        $options = array(
            'http' => array(
            'method'  => 'POST',
            'content' => json_encode($parameters),
            'header'=>  "Content-Type: application/json\r\n" .
                        "Accept: application/json\r\n"
            )
        );
        
        $context  = stream_context_create( $options );
        file_get_contents('https://api.telegram.org/bot'. config('app.iclaudia_telegram_token')  .'/sendMessage', false, $context );
        
    }

    public static function salvarMensagemCliente(BotCliente $cliente, string $mensagem) 
    {
        BotEnvios::create([
            'bot_cliente_id' => $cliente->id,
            'mensagem_cliente' => $mensagem
        ]);
    }

   

    public static function comando()
    {
        return 'Executou um comando';
    }

    
}