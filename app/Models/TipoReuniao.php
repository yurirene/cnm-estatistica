<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoReuniao extends Model
{
    protected $table = 'tipo_reunioes';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
}
