<?php

namespace App\Services;

use App\Models\Estado;
use App\Models\Federacao;
use App\Models\LogErro;
use App\Models\Pesquisa;
use App\Models\Regiao;
use App\Models\RegistroLogin;
use App\Models\Sinodal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DatatableAjaxService
{
   public static function logErros()
   {
        try {
            $logs = LogErro::select(['log_erros.id', 'log_erros.created_at', 'u.name', 'log'])
            ->join('users as u', 'u.id', 'user_id')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'dia' => Carbon::parse($item->created_at)->format('d/m/y H:i:s'),
                    'erro' => $item->log['message'],
                    'usuario' => $item->name,
                    'erro_completo' => $item->getRawOriginal('log')
                ];
            });

        return datatables()::of($logs)->make();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
        }
   }

   public static function informacaoFederacao(Federacao $federacao)
   {
        try {
            if (!$federacao) {
                return datatables()::of([])->make();
            }
            $informacoes = $federacao->locais->map(function($local) {
                $ultimo_relatorio = $local->relatorios->last();
                $total_socios = !is_null($ultimo_relatorio) ? $ultimo_relatorio->perfil['ativos'] + $ultimo_relatorio->perfil['cooperadores'] : 'Sem informação';
                $relatorio_entregue = (!is_null($ultimo_relatorio) && $ultimo_relatorio->ano_referencia == date('Y')) ? 'Entregue' : 'Pendente';
                return [
                    'nome_ump' => $local->nome,
                    'nro_socios' => $total_socios,
                    'status_relatorio' => $relatorio_entregue
                ];
            });
            return datatables()::of($informacoes)->make();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
        }
   }

   public static function acompanhamentoPesquisaSinodais(Pesquisa $pesquisa)
   {
        try {
            if (!$pesquisa) {
                return datatables()::of([])->make();
            }
            $responderam = $pesquisa->respostas()->whereHas('usuario.sinodais')->get()->pluck('sinodais');
            $sinodais = Sinodal::whereDoesntHave('') map(function($local) {
                $ultimo_relatorio = $local->relatorios->last();
                $total_socios = !is_null($ultimo_relatorio) ? $ultimo_relatorio->perfil['ativos'] + $ultimo_relatorio->perfil['cooperadores'] : 'Sem informação';
                $relatorio_entregue = (!is_null($ultimo_relatorio) && $ultimo_relatorio->ano_referencia == date('Y')) ? 'Entregue' : 'Pendente';
                return [
                    'nome_ump' => $local->nome,
                    'nro_socios' => $total_socios,
                    'status_relatorio' => $relatorio_entregue
                ];
            });
            return datatables()::of($sinodais)->make();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]); 
        }
   }
}
