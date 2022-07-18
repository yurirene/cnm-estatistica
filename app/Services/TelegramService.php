<?php

namespace App\Services;

use TelegramBot\Api\BotApi;


class TelegramService
{
    
    /**
     * Método Responsável poor enviar a mensagem
     * @param string $message
     * @return boolean
     */
    public static function sendMessage($message)
    {
        try {
            $obBotApi = new BotApi(config('app.telegram_token'));
            return $obBotApi->sendMessage(config('app.telegram_chat_id'), $message);
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
    
}