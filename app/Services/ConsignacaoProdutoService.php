<?php

namespace App\Services;

use App\Models\ConsignacaoProduto;
use App\Models\FluxoEstoqueProduto;
use App\Models\Produto;
use App\Models\User;

class ConsignacaoProdutoService
{

    public static function store(array $request) : ?ConsignacaoProduto
    {
        try {
            $fluxo = ConsignacaoProduto::create([
                'quantidade_consignada' => $request['quantidade_consignada'],
                'quantidade_retornada' => 0,
                'produto_id' => $request['produto_id'],
                'user_id' => $request['user_id'],
            ]);

            return $fluxo;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function update(ConsignacaoProduto $estoque, array $request) : ?ConsignacaoProduto
    {
        try {
            $estoque->update([
                'quantidade_consignada' => $request['quantidade_consignada'],
                'quantidade_retornada' => $request['quantidade_retornada'],
                'produto_id' => $request['produto_id'],
                'user_id' => $request['user_id'],
            ]);

            return $estoque;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function delete(ConsignacaoProduto $estoque) : void
    {
        try {
            $estoque->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function getUsuarios() : array
    {
        $usuarios = User::whereHas('roles', function($sql) {
            return $sql->whereIn('name', [
                'diretoria',
                'secretaria_eventos',
                'secreatria_produtos',
                'secretaria_evangelismo',
                'secretaria_responsabilidade'
            ]);
        })
        ->get()
        ->pluck('name', 'id')
        ->toArray();
        return $usuarios;
    }
}
