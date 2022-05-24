<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    
    use Uuid;
    
    protected $table = 'atividades';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['start'];

    public const TIPOS = [
        1 => 'Reunião da Diretoria',
        2 => 'Reunião com Sinodais',
        3 => 'Reuniao com Federações',
        4 => 'Comissão Executiva',
        6 => 'Programação CNM',
        7 => 'Programação Sinodal',
        8 => 'Programação Federação',
        9 => 'Palestra/Curso Sinodal',
        10 => 'Palestra/Curso Federação',
    ];

    public const CORES = [
        1 => 'red',
        2 => 'blue',
        3 => 'orange',
        4 => 'purple',
        6 => 'green',
        7 => 'yellow',
        8 => 'gray',
        9 => 'lime',
        10 => 'brown',
    ];

}
