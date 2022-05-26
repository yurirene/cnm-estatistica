<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Regiao extends Model
{       
    protected $table = 'regioes';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function estados()
    {
        return $this->hasMany(Estado::class);
    }

    public function sinodais()
    {
        return $this->hasMany(Sinodal::class);
    }

    public function federacoes()
    {
        return $this->hasMany(Federacao::class);
    }

    public function locais()
    {
        return $this->hasMany(Local::class);
    }

}
