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
use App\Models\Parametro;
use App\Models\Sinodal;
use App\Models\User;
use App\Services\Estatistica\EstatisticaService;
use App\Services\IClaudiaService;
use Illuminate\Support\Facades\Log;

class QuantidadeRelatoriosFaltantesStrategy implements ChatBotStrategy
{

    public static function process(BotCliente $cliente, string $mensagem)
    {
        try {
            $message = BotMessage::whereIdentificador('quantidade_relatorios_entregues')->first();
            $dados = self::getTotalizador($cliente);
            $params = [
                'params' => ['{texto}'],
                'propriedades' => [$dados]
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

    public static function getTotalizador(BotCliente $cliente)
    {
        try {
            $usuario = $cliente->usuario;
            $texto = '';

            if ($usuario->hasRole('diretoria')) {
                $texto .= self::getTotalizadorSinodais($usuario) . PHP_EOL;
                $sinodais = Sinodal::where('regiao_id', $usuario->regiao_id)
                    ->get()
                    ->pluck('id')
                    ->toArray();
                $texto .= self::getTotalizadorFederacoes($sinodais) . PHP_EOL;
                $federacoes = Federacao::whereIn('sinodal_id', $sinodais)->get()->pluck('id')->toArray();
                $texto .= self::getTotalizadorLocais($federacoes) . PHP_EOL;
            }

            if ($usuario->hasRole('sinodal')) {
                $texto .= self::getTotalizadorFederacoes([$usuario->sinodal_id]) . PHP_EOL;
                $federacoes = Federacao::where('sinodal_id', $usuario->sinodal_id)->get()->pluck('id')->toArray();
                $texto .= self::getTotalizadorLocais($federacoes) . PHP_EOL;
            }

            if ($usuario->hasRole('federacao')) {
                $texto .= self::getTotalizadorLocais([$usuario->federacao_id]) . PHP_EOL;
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
        $relatorios = Sinodal::where('regiao_id', $user->regiao_id)
            ->whereDoesntHave('relatorios', function($sql) {
                return $sql->where('ano_referencia', EstatisticaService::getAnoReferencia());
            })
            ->get()
            ->count();
        return '<b>Faltam (Sinodais)</b>: ' . $relatorios;
    }

    public static function getTotalizadorFederacoes(array $sinodais)
    {
        $relatorios = Federacao::whereIn('sinodal_id', $sinodais)
            ->whereDoesntHave('relatorios', function($sql) {
                return $sql->where('ano_referencia', EstatisticaService::getAnoReferencia());
            })
            ->get()
            ->count();
        return '<b>Faltam (Federações)</b>: ' . $relatorios;
    }
    public static function getTotalizadorLocais(array $federacoes)
    {
        $relatorios = Local::whereIn('federacao_id', $federacoes)
            ->whereDoesntHave('relatorios', function($sql) {
                return $sql->where('ano_referencia', EstatisticaService::getAnoReferencia());
            })
            ->get()
            ->count();
        return '<b>Faltam (UMPs Locais)</b>: ' . $relatorios;
    }

}
