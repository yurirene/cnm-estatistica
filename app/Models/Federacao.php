<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Federacao extends Model
{
    use Uuid;
    
    protected $table = 'federacoes';
    protected $guarded = ['id', 'created_at', 'updated_at'];


    public function locais()
    {
        return $this->hasMany(Local::class);
    }
}
