<?php

namespace App\Models\Apps\Site;

use App\Casts\DateCast;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use GenericTrait;
    protected $table = 'eventos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'form' => 'array',
        'data_inicio' => DateCast::class,
        'data_fim' => DateCast::class
    ];
    protected $dates = ['created_at', 'updated_at'];

}
