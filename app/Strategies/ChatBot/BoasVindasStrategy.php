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
        $message = BotMessage::whereName('boas_vindas')->first();
        $mensagem = str_replace('{nome}', $cliente->nome, $message);
        IClaudiaService::sendMessage($cliente, $message);
    }

}