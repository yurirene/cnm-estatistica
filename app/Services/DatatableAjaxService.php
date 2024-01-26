<?php

namespace App\Services;

use App\Models\Aviso;
use App\Models\Estado;
use App\Models\Federacao;
use App\Models\Local;
use App\Models\LogErro;
use App\Models\Parametro;
use App\Models\Pesquisas\Pesquisa;
use App\Models\Regiao;
use App\Models\RegistroLogin;
use App\Models\Sinodal;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Formularios\FormularioFederacaoService;
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
            $anoReferencia = EstatisticaService::getAnoReferencia();
            $informacoes = $federacao->locais->map(function($local) use ($anoReferencia) {
                $ultimoRelatorio = $local->relatorios->last();
                $totalSocios = !is_null($ultimoRelatorio)
                    ? $ultimoRelatorio->perfil['ativos'] + $ultimoRelatorio->perfil['cooperadores']
                    : 'Sem informação';
                $relatorioEntregue = (!is_null($ultimoRelatorio) && $ultimoRelatorio->ano_referencia == $anoReferencia)
                    ? 'Entregue'
                    : 'Pendente';
                $usuario = $local->usuario->first();
                return [
                    'nome_ump' => $local->nome,
                    'nro_socios' => $totalSocios,
                    'status_relatorio' => $relatorioEntregue,
                    'usuario_email' => $usuario->email,
                    'usuario_id' => $usuario->id
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
            $responderam = $pesquisa->respostas()
                ->whereHas('usuario.sinodais')
                ->get()
                ->pluck('usuario.sinodais')
                ->collapse()
                ->pluck('id');
            $nao_responderam = Sinodal::whereNotIn('id', $responderam)
                ->where('regiao_id', Auth::user()->regioes->first()->id)
                ->get()
                ->map(function($item) {
                    return [
                        'nome' => $item->nome,
                        'sigla' => $item->sigla
                    ];
                });

            return datatables()::of($nao_responderam)->make();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
   }


   public static function acompanhamentoPesquisaFederacoes(Pesquisa $pesquisa)
   {
        try {
            if (!$pesquisa) {
                return datatables()::of([])->make();
            }
            $responderam = $pesquisa->respostas()
                ->whereHas('usuario.federacoes')
                ->get()
                ->pluck('usuario.federacoes')
                ->collapse()
                ->pluck('id');
            $nao_responderam = Federacao::whereNotIn('id', $responderam)
                ->where('regiao_id', Auth::user()->regioes->first()->id)
                ->get()
                ->map(function($item) {
                    return [
                        'nome' => $item->nome,
                        'sigla' => $item->sigla ?? '-',
                        'sinodal' => $item->sinodal->sigla
                    ];
                });

            return datatables()::of($nao_responderam)->make();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
   }

   public static function acompanhamentoPesquisaLocais(Pesquisa $pesquisa)
   {
        try {
            if (!$pesquisa) {
                return datatables()::of([])->make();
            }
            $responderam = $pesquisa->respostas()
                ->whereHas('usuario.locais')
                ->get()
                ->pluck('usuario.locais')
                ->collapse()
                ->pluck('id');
            $nao_responderam = Local::whereNotIn('id', $responderam)
                ->where('regiao_id', Auth::user()->regioes->first()->id)
                ->get()
                ->map(function($item) {
                    return [
                        'nome' => $item->nome,
                        'federacao' => $item->federacao->sigla,
                        'sinodal' => $item->sinodal->sigla
                    ];
                });


            return datatables()::of($nao_responderam)->make();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
   }

   public static function formulariosEntregues(string $instancia, string $id = null)
   {
        try {
            $query = null;
            if ($instancia == 'Federacao') {
                $query = Federacao::when($id, function ($sql) use ($id) {
                        return $sql->where('sinodal_id', $id);
                    }, function ($sql) {
                        return $sql->where('sinodal_id', auth()->user()->sinodais->first()->id);
                    });
            }
            if ($instancia == 'Sinodal') {
                $query = Sinodal::when($id, function ($sql) use ($id) {
                        return $sql->where('regiao_id', $id);
                    }, function ($sql) {
                        return $sql->where('regiao_id', auth()->user()->regioes->first()->id);
                    });
            }
            if ($instancia == 'Local') {
                $query = Local::when($id, function ($sql) use ($id) {
                        return $sql->where('federacao_id', $id);
                    }, function ($sql) {
                        return $sql->where('federacao_id', auth()->user()->federacoes->first()->id);
                    });
            }
            $formulariosEntregues = $query
                ->where('status', true)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nome' => $item->nome,
                        'entregue' => $item->relatorios()
                            ->where('ano_referencia', Parametro::where('nome', 'ano_referencia')->first()->valor)
                            ->get()
                            ->count(),
                    ];
                });

        return datatables()::of($formulariosEntregues)->make();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
   }


    /**
     * Retorna lista das sinodais informando se entregaram os formulários
     * e a qualidade dos formulários
     */
    public static function getFormularioSinodais()
    {
        try {
            $dados = EstatisticaService::getDadosQualidadeEstatistica()->toArray();
            return datatables()::of($dados)->make();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }

    }


   public static function estatisticaFormulariosLocais(string $id)
   {
        try {
            $formulariosEntregues = Local::where('sinodal_id', $id)
                ->where('status', true)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nome' => $item->nome,
                        'entregue' => $item->relatorios()
                            ->where('ano_referencia', Parametro::where('nome', 'ano_referencia')->first()->valor)
                            ->get()
                            ->count(),
                    ];
                });

        return datatables()::of($formulariosEntregues)->make();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    /**
     * Listar usuários que visualizaram o aviso
     *
     * @param integer $id
     * @return void
     */
    public static function listarVisualizados(int $id)
    {

        try {
            $aviso = Aviso::find($id);
            $usuarios = $aviso->usuarios()
                ->get()
                ->map(function ($item) {
                    return [
                        'nome' => $item->instancia()->first()->nome,
                        'lido' => $item->pivot->visualizado
                    ];
                })
                ->toArray();

        return datatables()::of($usuarios)->make();
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

}
