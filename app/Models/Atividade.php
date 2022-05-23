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

}
