<?php

namespace App\Models;

use App\Services\UserService;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
