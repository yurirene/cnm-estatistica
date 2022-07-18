<?php

namespace App\Services;

class IClaudiaService
{
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