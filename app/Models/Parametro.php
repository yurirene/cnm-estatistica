<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    protected $table = 'parametros';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
