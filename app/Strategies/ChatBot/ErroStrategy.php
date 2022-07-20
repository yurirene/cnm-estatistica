<?php 

namespace App\Strategies\ChatBot;

use App\Interfaces\ChatBotStrategy;
use App\Models\BotCliente;
use App\Models\BotMessage;
use App\Services\IClaudiaService;
use Illuminate\Support\Facades\Log;

class ErroStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        $message = BotMessage::whereIdentificador('erro')->first();
        $params = [
            'params' => ['{mensagem}'],
            'propriedades' => [$mensagem]
        ];
        IClaudiaService::sendMessage($cliente, $message, $params);
    }

}