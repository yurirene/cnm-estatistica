<?php

namespace App\Models\Produtos;

use App\Traits\AuditoriaProdutosTrait;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use AuditoriaProdutosTrait;

    public string $nomeTabela = 'Pedidos';

    protected $table = 'pedidos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'produtos' => 'array',
        'updated_at' => 'date',
        'created_at' => 'date'
    ];


}
