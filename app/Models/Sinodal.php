<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\GenericTrait;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Sinodal extends Model
{
    use GenericTrait, SoftDeletes;
    
    protected $table = 'sinodais';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['data_organizacao'];

    public function regiao()
    {
        return $this->belongsTo(Regiao::class);
    }

    public function federacoes()
    {
        return $this->hasMany(Federacao::class);
    }

    public function usuario()
    {
        return $this->belongsToMany(User::class, 'usuario_sinodal');
    }

    public function scopeQuery($query)
    {
        if (Auth::user()->admin == true) {
            return $query;
        }
        return $query->whereIn('regiao_id', Auth::user()->regioes->pluck('id')->toArray());
    }

}
