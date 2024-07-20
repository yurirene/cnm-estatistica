<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class DiretoriaHistorico extends Model
{
    use Auditable;

    protected $table = 'diretoria_historicos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
