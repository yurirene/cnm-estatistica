<?php

namespace App\Models\Produtos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FluxoEstoqueProduto extends Model
{
    protected $table = 'fluxo_estoque_produtos';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
