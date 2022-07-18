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
        Log::info($request);
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

        BotEnvios::create([
            'bot_cliente_id' => $cliente->id,
            'mensagem_cliente' => $message
        ]);
        $ultima_mensagem_do_servidor = BotEnvios::where('bot_cliente_id', $cliente->id)->whereNotNull('mensagem_servidor')->get()->last();
        if (!$ultima_mensagem_do_servidor) {
            app()->make(MessageFactory::class)->makeMessage('BoasVindas')->process($cliente, $message);
        }
    }

    public static function sendMessage(BotCliente $cliente, BotMessage $messagem_servidor, array $params) 
    {
        BotEnvios::create([
            'bot_cliente_id' => $cliente->id,
            'mensagem_servidor' => $messagem_servidor->id,
        ]);

        

        $parameters = [
            'chat_id' => $cliente->chat_id, 
            "text" => str_replace($params['params'], $params['propriedades'], $messagem_servidor)
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
        file_get_contents('https://api.telegram.org/bot'. config('app.iclaudia_telegram_token')  .'/sendMessage?' . http_build_query($parameters), false, $context );
        
    }

   

    public static function comando()
    {
        return 'Executou um comando';
    }

    
}