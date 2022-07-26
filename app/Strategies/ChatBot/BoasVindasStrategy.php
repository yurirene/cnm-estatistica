<?php 

namespace App\Strategies\ChatBot;

use App\Interfaces\ChatBotStrategy;
use App\Models\BotCliente;
use App\Models\BotMessage;
use App\Services\IClaudiaService;
use Illuminate\Support\Facades\Log;

class BoasVindasStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        try {
            $message = BotMessage::whereIdentificador('boas_vindas')->first();
            $params = [
                'params' => ['{nome}'],
                'propriedades' => [$cliente->nome]
            ];
            IClaudiaService::sendMessage($cliente, $message, $params);
        } catch (\Throwable $th) {
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
            $message = BotMessage::whereIdentificador('boas_vindas')->first();
            $params = [
                'params' => ['{nome}'],
                'propriedades' => [$cliente->nome]
            ];
            IClaudiaService::sendMessage($cliente, $message, $params);
        } catch (\Throwable $th) {
            Log::error([
                'message' => $th->getMessage(), 
                'linha' => $th->getLine()
            ]);
        }
    }

}