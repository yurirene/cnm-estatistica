<?php

namespace App\Http\Controllers;

use App\Services\EnviarMsgService;
use App\Services\IClaudiaService;
use Illuminate\Http\Request;

class IClaudiaController extends Controller
{

    public static function process($request)
    {

        if (!$request['message']) {
            return;
        }

        $message = $request['message'];
        $message_id = $message['message_id'];
        $chat_id = $message['chat']['id'];

        if (!isset($message['text'])) {
            return;
        }

        $text = IClaudiaService::decode($message);

        $parameters = [
            'chat_id' => $chat_id, 
            "text" => $text
        ];

        IClaudiaService::sendMessage($parameters);

    }

   
}
