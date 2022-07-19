<?php 

namespace App\Strategies\ChatBot;

use App\Factories\MessageFactory;
use App\Interfaces\ChatBotStrategy;
use App\Models\BotCliente;
use App\Models\BotMessage;
use App\Models\Federacao;
use App\Models\FormularioLocal;
use App\Models\Local;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\IClaudiaService;
use Illuminate\Support\Facades\Log;

class TrocarUsuarioStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        try {    
            $message = BotMessage::whereIdentificador('trocar_usuario')->first();
            
            $cliente->update([
                'email' => null,
                'user_id' => null
            ]);

            IClaudiaService::sendMessage($cliente, $message);
            app()->make(MessageFactory::class)->makeMessage('Login')->process($cliente, $message);
        }  catch (\Throwable $th) {
            Log::error([
                'message' => $th->getMessage(), 
                'linha' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

}