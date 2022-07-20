<?php 

namespace App\Strategies\ChatBot;

use App\Factories\MessageFactory;
use App\Interfaces\ChatBotStrategy;
use App\Models\BotCliente;
use App\Models\BotMessage;
use App\Services\IClaudiaService;
use Illuminate\Support\Facades\Log;

class ListaOpcoesStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        try {
            $message = BotMessage::whereIdentificador('lista_opcoes')->first();
            $instancia = self::getInstancia($cliente);
            $params = [
                'params' => ['{instancia}'],
                'propriedades' => [$instancia]
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
            $message = BotMessage::whereIdentificador('lista_opcoes')->first();
            $resposta = BotMessage::where('resposta_de', $message->id)->where('keywords', $mensagem)->first(); 
            Log::info([
                'resposta' => $resposta,
                'message' => $message->id
            ]);
            if (is_null($resposta)) {
                app()->make(MessageFactory::class)->makeMessage('Erro')->process($cliente, $message);
            }
            $classe = str_replace('_', '', ucwords($resposta->identificador, '_'));
            app()->make(MessageFactory::class)->makeMessage($classe)->process($cliente, $message);
        } catch (\Throwable $th) {
            Log::error([
                'message' => $th->getMessage(), 
                'linha' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public static function getInstancia(BotCliente $cliente) : string
    {
        if ($cliente->usuario->hasRole('administrador')) {
            return 'Todas as UMPs';
        } else if ($cliente->usuario->hasRole('diretoria')) {
            return 'Sinodais';
        } else if ($cliente->usuario->hasRole('sinodal')) {
            return 'Federações';
        } else if ($cliente->usuario->hasRole('federacao')) {
            return 'UMPs Locais';
        } else {
            return '-';
        }
    }

}