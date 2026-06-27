<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public static function sendMessage(string $chatId, string $message): bool
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        if (empty($token) || empty($chatId)) {
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            return $response->successful();
        } catch (\Throwable $th) {
            Log::error('Falha ao enviar mensagem Telegram', [
                'message' => $th->getMessage(),
            ]);

            return false;
        }
    }
}
