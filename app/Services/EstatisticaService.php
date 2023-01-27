<?php

namespace App\Services;

use App\Exports\BaseDadosFormularioExport;
use App\Models\Federacao;
use App\Models\FormularioSinodal;
use App\Models\Local;
use App\Models\Parametro;
use App\Models\Ranking;
use App\Models\Sinodal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EstatisticaService
{

    public static function atualizarParametro(array $request) : void
    {
        $valor = $request['valor'];
        if (in_array($valor,['true', 'false'])) {
            $valor = $request['valor'] == 'true' ? 'SIM' : 'NAO';
        }
        try {
            $parametro = Parametro::find($request['id']);
            $parametro->update([
                'valor' => $valor
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getParametros() : Collection
    {
        try {
            return Parametro::where('area', 'estatistica')->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'nome' => $item->nome,
                    'valor' => $item->valor,
                    'label' => $item->descricao,
                    'tipo' => $item->tipo
                ];
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getAnoReferenciaFormularios() : array
    {
        return FormularioSinodal::selectRaw('DISTINCT(ano_referencia) as ano_referencia')
        ->groupBy('ano_referencia')
        ->get()
        ->pluck('ano_referencia', 'ano_referencia')
        ->toArray();
    }

    public static function exportarExcel(array $request)
    {
        $formulario_base = FormularioSinodal::with(['sinodal', 'sinodal.regiao'])->where('ano_referencia', $request['ano_referencia'])->first()->toArray();
        $dados = collect($formulario_base)->except([
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'sinodal_id',
            'sinodal.id',
            'sinodal.sigla',
            'sinodal.data_organizacao',
            'sinodal.sinodo',
            'sinodal.midias_sociais',
            'sinodal.regiao_id',
            'sinodal.status',
            'sinodal.created_at',
            'sinodal.updated_at',
            'sinodal.deleted_at'
        ]);
        $campos = [];
        $somente_colunas = [];
        foreach ($dados as $coluna_master => $coluna) {
            if (!is_array($coluna)) {
                $campos[] = $coluna_master;
                $somente_colunas[] = $coluna_master;
                continue;
            }
            $campos[$coluna_master] = array_keys($coluna);
            array_push($somente_colunas, ...array_keys($coluna));
        }
        return Excel::download(new BaseDadosFormularioExport($campos, $somente_colunas, $request['ano_referencia']), 'base_dados_' . date('d_m_Y') . '.xlsx');

    }

    /**
     * Retorna a collection com os ids das federações e se entregaram os relatórios
     */
    public static function getDadosFormularioFederacao(string $idSinodal, int $ano): Collection
    {
        try {
            return Federacao::where('sinodal_id', $idSinodal)
            ->where('status', true)
            ->get()
            ->map(function ($item) use ($ano) {
                return [
                    'id' => $item->id,
                    'formulario' => $item->relatorios()
                        ->where('ano_referencia', $ano)
                        ->get()
                        ->count(),
                ];
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Retorna a porcentagem formatada da relação de formuários preenchidos das federações
     */
    public static function getPorcentagemFormularioFederacao(string $idSinodal, int $ano): string
    {
        try {
            $federacoes = self::getDadosFormularioFederacao($idSinodal, $ano);
            $formularios = $federacoes->where('formulario', '!=', 0)->count();
            $porcentagem = 0;
            if ($federacoes->count() != 0) {
                $porcentagem = round(($formularios * 100) / $federacoes->count(), 2);
            }
            return "{$porcentagem}% ({$formularios} de {$federacoes->count()})"; 
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Retorna a collection com os ids das locais e se entregaram os relatórios
     */
    public static function getDadosFormularioLocal(string $idSinodal, int $ano): Collection
    {
        try {
            return Local::where('sinodal_id', $idSinodal)
            ->where('status', true)
            ->get()
            ->map(function ($item) use ($ano) {
                return [
                    'id' => $item->id,
                    'formulario' => $item->relatorios()
                        ->where('ano_referencia', $ano)
                        ->get()
                        ->count(),
                ];
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Retorna a porcentagem formatada da relação de formuários preenchidos das locais
     */
    public static function getPorcentagemFormularioLocal($idSinodal, $ano): string
    {
        try {
            $locais = self::getDadosFormularioLocal($idSinodal, $ano);
            $formularios = $locais->where('formulario', '!=', 0)->count();
            $porcentagem = 0;
            if ($locais->count() != 0) {
                $porcentagem = round(($formularios * 100) / $locais->count(), 2);
            }
            return "{$porcentagem}% ({$formularios} de {$locais->count()})"; 
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Faz o calculo da qualidade do relatório, levando em consideração
     * somente os relatórios das locais que fazem parte das federações que entregaram
     */
    public static function calcularQualidade(string $sinodal, int $ano): int
    {
        try {
            $federacoes = Federacao::where('sinodal_id', $sinodal)
                ->whereHas('relatorios', function ($sql) use ($ano) {
                    return $sql->where('ano_referencia', $ano);
                })
                ->get()
                ->pluck('id');
            $total_locais = $locais = Local::where('sinodal_id', $sinodal)
                ->where('status', true)
                ->get()
                ->count();
            $locais = Local::where('status', true)
                ->whereIn('federacao_id', $federacoes)
                ->get()
                ->map(function ($item) use ($ano) {
                    return [
                        'id' => $item->id,
                        'formulario' => $item->relatorios()
                            ->where('ano_referencia', $ano)
                            ->get()
                            ->count(),
                    ];
                });
            $formularios = $locais->where('formulario', '!=', 0)->count();
            if ($locais->count() == 0) {
                return 0;
            }
            $qualidade = ($formularios * 100) / $total_locais;
            return ceil($qualidade) > 100 ? 100 : ceil($qualidade);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Lista os dados e orderna por qualidade salvando ou atualizando
     * posteriormente no banco de dados
     */
    public static function atualizarRanking()
    {
        try {
            $dados = self::getDadosQualidadeEstatistica()
                ->sortByDesc('qualidade')
                ->groupBy('qualidade');
            $posicao = 1;
            foreach ($dados as $dado) {
                foreach ($dado as $sinodal) {
                    Ranking::updateOrCreate([
                        'sinodal_id' => $sinodal['id'],
                        'ano_referencia' => $sinodal['ano']
                    ], [
                        'sinodal_id' => $sinodal['id'],
                        'pontuacao' => $sinodal['qualidade'],
                        'ano_referencia' => $sinodal['ano'],
                        'posicao' => $posicao,
                        'form_fed_entregue' => $sinodal['qtd_fomr_fed'],
                        'form_local_entregue' => $sinodal['qtd_fomr_local']
                    ]);
                }
                $posicao++;
            }
        } catch (\Throwable $th) {
            dd($th->getMessage(), $th->getLine(), $th->getFile());
            throw $th;
        }
    }

    /**
     * Retorna uma collection contendo os dados necessários para atulizar o ranking
     * e listar no dataTable de Relatórios Estatísticos
     */
    public static function getDadosQualidadeEstatistica(): Collection
    {
        $ano = Parametro::where('nome', 'ano_referencia')->first()->valor;
        return Sinodal::where('status', true)
            ->orderBy('regiao_id')
            ->orderBy('nome')
            ->get()
            ->map(function ($item) use ($ano) {
                $nome = str_replace('Sinodal', 'S', $item->nome);
                $nome = str_replace('Confederação', 'C', $nome);
                $nome = str_replace(['Mocidade', 'Mocidades'], 'M', $nome);
                return [
                    'id' => $item->id,
                    'ano' => $ano,
                    'nome' => $nome,
                    'entregue' =>  $item->relatorios()
                        ->where('ano_referencia', $ano)
                        ->get()
                        ->count(),
                    'federacoes' => self::getPorcentagemFormularioFederacao($item->id, $ano),
                    'locais' => self::getPorcentagemFormularioLocal($item->id, $ano),
                    'qtd_fomr_fed' => self::getDadosFormularioFederacao($item->id, $ano)->where('formulario', '!=', 0)->count(),
                    'qtd_fomr_local' => self::getDadosFormularioLocal($item->id, $ano)->where('formulario', '!=', 0)->count(),
                    'qualidade' => self::calcularQualidade($item->id, $ano)
                ];
            });
    }
}
