<?php

namespace App\Models\Produtos;

use App\Casts\DateCast;
use App\Casts\FileCast;
use App\Casts\MoneyCast;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FluxoCaixa extends Model
{
    use GenericTrait;

    protected $table = 'produtos_fluxo_caixa';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'valor' => MoneyCast::class,
        'comprovante' => FileCast::class,
        'data_lancamento' => DateCast::class
    ];
    protected $dates = ['created_at', 'updated_at'];
    public $caminho = 'public/produtos/comprovantes';

    public const SALDO_INICIAL = 0;
    public const ENTRADA = 1;
    public const SAIDA = 2;

    public const LABELS_TIPOS = [
        self::SALDO_INICIAL => 'info',
        self::ENTRADA => 'success',
        self::SAIDA => 'danger'
    ];

    public const TIPOS = [
        self::SALDO_INICIAL => 'Saldo Inicial',
        self::ENTRADA => 'Entrada',
        self::SAIDA => 'Saída'
    ];

    public const TIPOS_ATIVOS = [
        self::ENTRADA => 'Entrada',
        self::SAIDA => 'Saída'
    ];

}
