<?php 

namespace App\Strategies\ChatBot;

use App\Interfaces\ChatBotStrategy;
use App\Models\BotCliente;
use App\Models\BotMessage;
use App\Services\IClaudiaService;

class ListaOpcoesStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        $message = BotMessage::whereIdentificador('lista_opcoes')->first();
        $instancia = self::getInstancia($cliente);
        $params = [
            'params' => ['{instancia}'],
            'propriedades' => [$cliente->usuario->instancia]
        ];
        IClaudiaService::sendMessage($cliente, $message, $params);
    }


    public static function processReply(BotCliente $cliente, string $mensagem)
    {
        $message = BotMessage::whereIdentificador('boas_vindas')->first();
        $params = [
            'params' => ['{nome}'],
            'propriedades' => [$cliente->nome]
        ];
        IClaudiaService::sendMessage($cliente, $message, $params);
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
        }
    }

}