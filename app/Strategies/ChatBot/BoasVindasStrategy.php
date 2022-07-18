<?php 

namespace App\Strategies\ChatBot;

use App\Interfaces\ChatBotStrategy;
use App\Models\BotCliente;
use App\Models\BotMessage;
use App\Services\IClaudiaService;

class BoasVindasStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        $message = BotMessage::whereIdentificador('boas_vindas')->first();
        $params = [
            'params' => ['{nome}'],
            'propriedade' => [$cliente->nome]
        ];
        IClaudiaService::sendMessage($cliente, $message, $params);
    }

}