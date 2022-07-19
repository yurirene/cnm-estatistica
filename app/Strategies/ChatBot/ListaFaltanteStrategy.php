<?php 

namespace App\Strategies\ChatBot;

use App\Factories\MessageFactory;
use App\Interfaces\ChatBotStrategy;
use App\Models\BotCliente;
use App\Models\BotMessage;
use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioLocal;
use App\Models\FormularioSinodal;
use App\Models\Local;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\IClaudiaService;
use Illuminate\Support\Facades\Log;

class ListaFaltanteStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        try {
            $message = BotMessage::whereIdentificador('lista_faltante')->first();
            $dados = self::getLista($cliente);
            $instancia = self::getInstancia($cliente);
            $params = [
                'params' => ['{lista}', '{instancia}'],
                'propriedades' => [$dados, $instancia]
            ];
            IClaudiaService::sendMessage($cliente, $message, $params);
            app()->make(MessageFactory::class)->makeMessage('ListaOpcoes')->process($cliente, $mensagem);
        }  catch (\Throwable $th) {
            Log::error([
                'message' => $th->getMessage(), 
                'linha' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public static function getLista(BotCliente $cliente)
    {
        try {
            
            $usuario = $cliente->usuario;
            $texto = '';
            if ($usuario->hasRole('diretoria')) {
                $texto .= self::getTotalizadorSinodais($usuario) . PHP_EOL;
            }
            if ($usuario->hasRole('sinodal')) {
                $texto .= self::getTotalizadorFederacoes($usuario->sinodais->pluck('id')->toArray()) . PHP_EOL;
            }
            if ($usuario->hasRole('federacao')) {
                $texto .= self::getTotalizadorLocais($usuario->federacoes->pluck('id')->toArray()) . PHP_EOL;
            }
            return $texto;
        }  catch (\Throwable $th) {
            Log::error([
                'message' => $th->getMessage(), 
                'linha' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public static function getTotalizadorSinodais(User $user)
    {
        $sinodais = Sinodal::whereIn('regiao_id', $user->regioes->pluck('id'))
            ->whereDoesntHave('relatorios', function($sql) {
                return $sql->where('ano_referencia', date('Y'));
            })
            ->get();
        $texto = '';
        foreach ($sinodais as $sinodal) {
            $texto .= '#' . $sinodal->nome . PHP_EOL;
        }
        return $texto;
    }

    public static function getTotalizadorFederacoes(array $sinodais)
    {
        $federacoes = Federacao::whereIn('sinodal_id', $sinodais)
            ->whereDoesntHave('relatorios', function($sql) {
                return $sql->where('ano_referencia', date('Y'));
            })
            ->get();

        $texto = '';
        foreach ($federacoes as $federacao) {
            $texto .= '#' . $federacao->nome . PHP_EOL;
        }
        return $texto;
    }
    public static function getTotalizadorLocais(array $federacoes)
    {
        $locais = Local::whereIn('federacao_id', $federacoes)
            ->whereDoesntHave('relatorios', function($sql) {
                return $sql->where('ano_referencia', date('Y'));
            })
            ->get();
        $texto = '';
        foreach ($locais as $local) {
            $texto .= '#' . $local->nome . PHP_EOL;
        }
        return $texto;
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