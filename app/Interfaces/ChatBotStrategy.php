<?php

namespace App\Interfaces;

use App\Models\BotCliente;

interface ChatBotStrategy 
{
    public static function process(BotCliente $cliente, string $mensagem);
}