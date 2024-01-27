<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiretoriaInformacao extends Model
{
    use GenericTrait;

    protected $table = 'diretoria_informacoes';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
