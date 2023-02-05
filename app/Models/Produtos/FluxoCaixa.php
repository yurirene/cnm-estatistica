<?php

namespace App\Models\Produtos;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FluxoCaixa extends Model
{
    use GenericTrait;

    protected $table = 'produtos_fluxo_caixa';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public const SALDO_INICIAL = 0;
    public const ENTRADA = 1;
    public const SAIDA = 2;

    public const TIPOS = [
        self::SALDO_INICIAL => 'Saldo Inicial',
        self::ENTRADA => 'Entrada',
        self::SAIDA => 'Saída'
    ];
}
