<?php 

namespace App\Strategies\ChatBot;

use App\Interfaces\ChatBotStrategy;
use App\Models\BotCliente;
use App\Models\BotMessage;
use App\Services\IClaudiaService;

class InformacoesUsuarioStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        $message = BotMessage::whereIdentificador('informacoes_usuario')->first();
        $params = [
            'params' => ['{usuario}', '{ump}'],
            'propriedades' => [
                $cliente->usuario->name,
                self::getInstancia($cliente)
            ]
        ];
        IClaudiaService::sendMessage($cliente, $message, $params);
    }

    public static function getInstancia(BotCliente $cliente) : array
    {
        if ($cliente->usuario->hasRole('administrador')) {
            return 'Admin';
        } else if ($cliente->usuario->hasRole('diretoria')) {
            return 'Confederação Nacional de Mocidades';
        } else if ($cliente->usuario->hasRole('sinodal')) {
            return $cliente->usuario->sinodais->first()->nome;
        } else if ($cliente->usuario->hasRole('federacao')) {
            return $cliente->usuario->federacoes->first()->nome;
        } else if ($cliente->usuario->hasRole('local')) {
            return $cliente->usuario->locais->first()->nome;
        } else {
            return 'Não Identificado';
        }
    }

}