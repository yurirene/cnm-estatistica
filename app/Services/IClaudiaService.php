<?php

namespace App\Services;

use App\Models\BotCliente;
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

        

        $text = IClaudiaService::decode($message);

        $parameters = [
            'chat_id' => $chat_id, 
            "text" => $text
        ];

        IClaudiaService::sendMessage($parameters);

    }

    public static function sendMessage($parameters) 
    {
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

    public static function decode($message)
    {
        return 'comando';
        $functions = [
            '/' => 'comando'
        ];
        return self::$$functions[$message];
    }

    public static function comando()
    {
        return 'Executou um comando';
    }

    
}