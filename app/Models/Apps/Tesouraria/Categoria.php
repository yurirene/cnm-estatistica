<?php

namespace App\Models\Apps\Tesouraria;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use GenericTrait;

    protected $table = 'tesouraria_categorias';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
