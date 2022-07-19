<?php 

namespace App\Strategies\ChatBot;

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

class QuantidadeInstanciasCadastradasStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        $message = BotMessage::whereIdentificador('quantidade_instancias_cadastradas')->first();
        $dados = self::getTotalizador($cliente);
        $params = [
            'params' => ['{texto}'],
            'propriedades' => [$dados]
        ];
        IClaudiaService::sendMessage($cliente, $message, $params);
        app()->make(MessageFactory::class)->makeMessage('ListaOpcoes')->process($cliente, $mensagem);
    }

    public static function getTotalizador(BotCliente $cliente)
    {
       try {
            $usuario = $cliente->usuario;
            $texto = '';
            if ($usuario->hasRole('diretoria')) {
                $texto .= self::getTotalizadorSinodais($usuario) . PHP_EOL;
                $sinodais = Sinodal::whereIn('regiao_id', $usuario->regioes->pluck('id'))->get()->pluck('id');
                $texto .= self::getTotalizadorFederacoes($sinodais) . PHP_EOL;
                $federacoes = Federacao::whereIn('sinodal_id', $sinodais)->get()->pluck('id');
                $texto .= self::getTotalizadorLocais($federacoes) . PHP_EOL;
            }
            if ($usuario->hasRole('sinodal')) {

                $texto .= self::getTotalizadorFederacoes($usuario->sinodais->pluck('id')) . PHP_EOL;
                $federacoes = Federacao::whereIn('sinodal_id', $usuario->sinodais->pluck('id'))->get()->pluck('id');
                $texto .= self::getTotalizadorLocais($federacoes) . PHP_EOL;
            }
            if ($usuario->hasRole('federacao')) {
                $texto .= self::getTotalizadorLocais($usuario->federacoes->pluck('id')) . PHP_EOL;
            }
            return $texto;
       } catch (\Throwable $th) {
           Log::error([$th->getMessage(), $th->getLine()]);
       }
    }

    public static function getTotalizadorSinodais(User $user)
    {
        $sinodais = Sinodal::whereIn('regiao_id', $user->regioes->pluck('id'))->count();
        return '<b>Total de Sinodais</b>: ' . $sinodais;
    }

    public static function getTotalizadorFederacoes(array $sinodais)
    {
        $federacoes = Federacao::whereIn('sinodal_id', $sinodais)->count();
        return '<b>Total de Federações</b>: ' . $federacoes;
    }
    public static function getTotalizadorLocais(array $federacoes)
    {
        $locais = Local::whereIn('federacao_id', $federacoes)->count();
        return '<b>Total de UMPs Locais</b>: ' . $locais;
    }

}