<?php

namespace App\Models\Apps\Site;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoInscrito extends Model
{
    protected $table = 'evento_inscritos';
    protected $guarded = ['created_at', 'updated_at'];
    protected $casts = [
        'informacoes' => 'array'
    ];
}
