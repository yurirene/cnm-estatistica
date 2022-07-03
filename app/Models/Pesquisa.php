<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesquisa extends Model
{
    protected $table = 'pesquisas';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = ['formulario' => 'array'];

}
