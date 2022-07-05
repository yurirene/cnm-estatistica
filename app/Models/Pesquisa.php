<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;

class Pesquisa extends Model
{

    use GenericTrait;
    
    protected $table = 'pesquisas';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = ['referencias' => 'array'];

}
