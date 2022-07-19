<?php

namespace App\Services;

use App\Factories\MessageFactory;
use App\Models\BotCliente;
use App\Models\BotEnvios;
use App\Models\BotMessage;
use Illuminate\Support\Facades\Log;

class IClaudiaService
{

    public static function processar(array $request)
    {
        $message = $request['message'];
        $nome = $message['chat']['first_name'];
        $chat_id = $message['chat']['id'];

        if (!isset($message['text'])) {
            return;
        }

        $cliente = BotCliente::firstOrCreate([
            'chat_id' => $chat_id,
            'nome' => $nome
        ]);       

        IClaudiaService::decode($cliente, $message['text']);
    }

    public static function decode(BotCliente $cliente, string $message)
    {

        self::salvarMensagemCliente($cliente, $message);

        $ultima_mensagem_do_servidor = BotEnvios::where('bot_cliente_id', $cliente->id)->whereNotNull('mensagem_servidor')->get()->last();
        if (!$ultima_mensagem_do_servidor) {
            app()->make(MessageFactory::class)->makeMessage('BoasVindas')->process($cliente, $message);
        } else {
            self::getResposta($cliente, $message);
        }
    }

    public static function getResposta(BotCliente $cliente, string $mensagem)
    {
        $lastMessage = $cliente->envios->whereNotNull('mensagem_server')->last()->mensagem->name;
        $mensagem = BotMessage::where(function ($q) use ($lastMessage, $mensagem) {
                $q->whereLike('keywords', $mensagem)
                ->whereHas('mensagem', function($q) use ($lastMessage) {
                    $q->whereIdentificador($lastMessage);
                });
            })
            ->orWhere(function ($q) use ($lastMessage) {
                $q->whereHas('mensagem', function($q) use ($lastMessage) {
                    $q->whereIdentificador($lastMessage);
                })->whereNull('keywords');
            })->first();
        Log::info($mensagem);
        // return [
        //     'className' => str_replace('_', '', ucwords($lastMessage, '_')),
        //     'name' => $mensagem
        // ];
    }

    public static function sendMessage(BotCliente $cliente, BotMessage $mensagem_servidor, array $params) 
    {
        BotEnvios::create([
            'bot_cliente_id' => $cliente->id,
            'mensagem_servidor' => $mensagem_servidor->id,
        ]);

        $texto = str_replace($params['params'], $params['propriedades'], $mensagem_servidor->mensagem);
        $texto = str_replace('\\n', PHP_EOL, $texto);        

        $parameters = [
            'chat_id' => $cliente->chat_id, 
            "text" => $texto
        ];

        $options = array(
            'http' => array(
            'method'  => 'POST',
            'content' => json_encode($parameters),
            'header'=>  "Content-Type: application/json\r\n" .
                        "Accept: application/json\r\n"
            )
        );
        
        $context  = stream_context_create( $options );
        file_get_contents('https://api.telegram.org/bot'. config('app.iclaudia_telegram_token')  .'/sendMessage', false, $context );
        
    }

    public static function salvarMensagemCliente(BotCliente $cliente, string $mensagem) 
    {
        BotEnvios::create([
            'bot_cliente_id' => $cliente->id,
            'mensagem_cliente' => $mensagem
        ]);
    }

   

    public static function comando()
    {
        return 'Executou um comando';
    }

    
}