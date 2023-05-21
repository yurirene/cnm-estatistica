<?php

namespace App\Models\ComissaoExecutiva;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reuniao extends Model
{
    protected $table = 'comissao_executiva_reunioes';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
