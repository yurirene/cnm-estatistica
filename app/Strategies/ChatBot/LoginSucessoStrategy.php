<?php 

namespace App\Strategies\ChatBot;

use App\Interfaces\ChatBotStrategy;
use App\Models\BotCliente;
use App\Models\BotMessage;
use App\Services\IClaudiaService;

class LoginSucessoStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        $message = BotMessage::whereIdentificador('login_successo')->first();
        IClaudiaService::sendMessage($cliente, $message);
    }

}