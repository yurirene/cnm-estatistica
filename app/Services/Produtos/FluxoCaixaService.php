<?php

namespace App\Services\Produtos;

use App\Models\Produtos\ConsignacaoProduto;
use App\Models\Produtos\FluxoCaixa;
use App\Models\Produtos\Produto;
use Illuminate\Support\Facades\Storage;

class FluxoCaixaService
{
    public static function store(array $request) : ?FluxoCaixa
    {
        try {
            return FluxoCaixa::create([
                'descricao' => $request['descricao'],
                'valor' => $request['valor'],
                'tipo' => $request['tipo'],
                'data_lancamento' => $request['data_lancamento'],
                'comprovante' => $request['comprovante'] ?? null
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function update(FluxoCaixa $fluxo, array $request) : ?FluxoCaixa
    {
        try {
            $fluxo->update([
                'descricao' => $request['descricao'],
                'valor' => $request['valor'],
                'tipo' => $request['tipo'],
                'data_lancamento' => $request['data_lancamento'],
                'comprovante' => $request['comprovante'] ?? null
            ]);
            return $fluxo;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function delete(FluxoCaixa $fluxo)
    {
        try {
            if (!empty($fluxo->comprovante)) {
                Storage::delete($fluxo->getRawOriginal('comprovante'));
            }
            $fluxo->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }



    public static function getTotalizadores()
    {
        $entradas = 0;
        $saidas = 0;
        $saldo_inicial = FluxoCaixa::where('tipo', FluxoCaixa::SALDO_INICIAL)->first()
            ? FluxoCaixa::where('tipo', FluxoCaixa::SALDO_INICIAL)->first()->getRawOriginal('valor')
            : 0;

        $lancamentos = FluxoCaixa::get();
        foreach ($lancamentos as $lancamento) {
            if ($lancamento->tipo == FluxoCaixa::ENTRADA) {
                $entradas += $lancamento->getRawOriginal('valor');
            }
            if ($lancamento->tipo == FluxoCaixa::SAIDA) {
                $saidas += $lancamento->getRawOriginal('valor');
            }
        }
        $saldo = ($saldo_inicial + $entradas) - $saidas;
        return [
            'entradas' => number_format($entradas, 2, ',', '.'),
            'saidas' => number_format($saidas, 2, ',', '.'),
            'saldo' => number_format($saldo, 2, ',', '.')
        ];
    }

    /**
     * Retorna se já existe saldo inicial cadastrado
     *
     * @return boolean
     */
    public static function validaSaldoInicial(): bool
    {
        return FluxoCaixa::where('tipo', 0)->first()
            ? true
            : false;
    }
}
