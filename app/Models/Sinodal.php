<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sinodal extends Model
{
    use Uuid;
    
    protected $table = 'sinodais';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function regiao()
    {
        return $this->belongsTo(Regiao::class);
    }

    public function federacoes()
    {
        return $this->hasMany(Federacao::class);
    }

}
