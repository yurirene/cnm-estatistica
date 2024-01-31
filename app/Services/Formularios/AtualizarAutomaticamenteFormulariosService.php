<?php

namespace App\Services\Formularios;

use App\Models\Federacao;
use App\Models\FormularioFederacao;
use App\Models\FormularioSinodal;
use App\Models\Local;
use App\Services\Estatistica\EstatisticaService;
use App\Services\Formularios\Totalizadores\TotalizadorFormularioSinodalService;
use App\Services\LogErroService;
use Illuminate\Database\Eloquent\Model;

class AtualizarAutomaticamenteFormulariosService
{

    public static function atualizarFederacao(Model $model) : void
    {
        try {
            $local = $model->local;
            $anoReferencia = EstatisticaService::getAnoReferencia();
            $formularioFederacao = FormularioFederacao::where('federacao_id', $local->federacao_id)
                ->where('ano_referencia', $anoReferencia)
                ->get();
            if ($formularioFederacao->isEmpty()) {
                $formularioFederacao = FormularioFederacao::create([
                    'federacao_id' => $local->federacao_id,
                    'ano_referencia' => $anoReferencia,
                    'status' => EstatisticaService::FORMULARIO_NAO_RESPONDIDO
                ]);

            } else {
                $formularioFederacao = $formularioFederacao->first();
            }
            $totalizador = FormularioFederacaoService::totalizador($local->federacao_id);
            $formularioFederacao->update([
                'perfil' => $totalizador['perfil'],
                'estado_civil' => $totalizador['estado_civil'],
                'escolaridade' => $totalizador['escolaridade'],
                'deficiencias' => $totalizador['deficiencias'],
                'programacoes_locais' => $totalizador['programacoes'],
            ]);

            self::verificarStatusEntregaFormularioFederacao($local->federacao_id);
            self::atualizarSinodal($formularioFederacao);
        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public static function atualizarSinodal(Model $model) : void
    {
        try {

            $federacao = $model->federacao;
            $anoReferencia = EstatisticaService::getAnoReferencia();
            $formularioSinodal = FormularioSinodal::where('sinodal_id', $federacao->sinodal_id)
                ->where('ano_referencia', $anoReferencia)
                ->get();

            if ($formularioSinodal->isEmpty()) {
                $formularioSinodal = FormularioSinodal::create([
                    'sinodal_id' => $federacao->sinodal_id,
                    'ano_referencia' => $anoReferencia,
                    'status' => EstatisticaService::FORMULARIO_NAO_RESPONDIDO
                ]);
            } else {
                $formularioSinodal = $formularioSinodal->first();
            }

            $totalizador = TotalizadorFormularioSinodalService::totalizador($federacao->sinodal_id);
            $formularioSinodal->update([
                'perfil' => $totalizador['perfil'],
                'estado_civil' => $totalizador['estado_civil'],
                'escolaridade' => $totalizador['escolaridade'],
                'deficiencias' => $totalizador['deficiencias'],
                'programacoes_federacoes' => $totalizador['programacoes_federacao'],
                'programacoes_locais' => $totalizador['programacoes_locais'],
            ]);

            self::verificarStatusEntregaFormularioSinodal($federacao->sinodal_id);

        } catch (\Throwable $th) {
            LogErroService::registrar([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            throw $th;
        }
    }

    /**
     * Verifica se a federação atingiu a quantidade mínima para converter o status para
     * App\Services\Estatistica\EstatisticaService::FORMULARIO_RESPOSTA_PARCIAL
     * caso não tenha sido entregue ainda
     *
     * @param string $idFederacao
     * @return void
     */
    public static function verificarStatusEntregaFormularioFederacao(string $idFederacao): void
    {
        $anoReferencia = EstatisticaService::getAnoReferencia();
        $formularioFederacao = FormularioFederacao::where('federacao_id', $idFederacao)
            ->where('ano_referencia', $anoReferencia)
            ->first();
        if ($formularioFederacao->status == EstatisticaService::FORMULARIO_ENTREGUE) {
            return;
        }

        $porcentagem = EstatisticaService::getValorPorcentagemEntregaFormularioUMPLocal($idFederacao, $anoReferencia);

        if  ($porcentagem >= EstatisticaService::getPorcentagemMinimaEntrega('federacao')) {
            $formularioFederacao->status = EstatisticaService::FORMULARIO_RESPOSTA_PARCIAL;
            $formularioFederacao->save();
        }
    }

    /**
     * Verifica se a sinodal atingiu a quantidade mínima para converter o status para
     * App\Services\Estatistica\EstatisticaService::FORMULARIO_RESPOSTA_PARCIAL
     * caso o formulário ainda não tenha sido entregue
     *
     * @param string $idSinodal
     * @return void
     */
    public static function verificarStatusEntregaFormularioSinodal(string $idSinodal): void
    {
        $anoReferencia = EstatisticaService::getAnoReferencia();
        $formularioSinodal = FormularioSinodal::where('sinodal_id', $idSinodal)
            ->where('ano_referencia', $anoReferencia)
            ->first();

        if ($formularioSinodal->status == EstatisticaService::FORMULARIO_ENTREGUE) {
            return;
        }

        $porcentagem = EstatisticaService::getValorPorcentagemEntregaFormularioFederacao(
            $idSinodal,
            $anoReferencia
        );

        if  ($porcentagem >= EstatisticaService::getPorcentagemMinimaEntrega('sinodal')) {
            $formularioSinodal->status = EstatisticaService::FORMULARIO_RESPOSTA_PARCIAL;
            $formularioSinodal->save();
        }
    }
}
