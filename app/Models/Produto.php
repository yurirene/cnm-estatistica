<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $table = 'produtos';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function getValorFormatadoAttribute()
    {
        return 'R$' . number_format($this->valor, 2, ',', '.');
    }
}
