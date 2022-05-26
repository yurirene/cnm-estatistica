<?php

namespace App\Models;

use App\Traits\Uuid;
use Database\Seeders\RegiaoSeeder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use Uuid;
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function regioes()
    {
        return $this->belongsToMany(Regiao::class, 'usuario_regiao');
    }

    public function atividades()
    {
        return $this->hasMany(Atividade::class);
    }

    public function perfis()
    {
        return $this->belongsToMany(Perfil::class, 'perfil_usuario');
    }

    public function sinodais()
    {
        return $this->belongsToMany(Sinodal::class, 'usuario_sinodal');
    }

    public function federacoes()
    {
        return $this->belongsToMany(Federacao::class, 'usuario_federacao');
    }

    public function locais()
    {
        return $this->belongsToMany(Local::class, 'usuario_local');
    }

    public function scopeQuery($query)
    {
        if (Auth::user()->admin) {
            return $query;
        }
        return $query->whereDoesntHave('perfis', function($sql) {
            return $sql->whereIn('nome', ['cnm']);
        })
        ->when(in_array('cnm', Auth::user()->perfis->pluck('nome')->toArray()), function($sql) {
            return $sql->whereHas('sinodais', function ($q) {
                return $q->whereIn('regiao_id', Auth::user()->regioes->pluck('id')->toArray());
            })->orWhereHas('perfis', function ($q) {
                return $q->where('nome', 'secretario');
            });
        })
        ->when(in_array('sinodal', Auth::user()->perfis->pluck('nome')->toArray()), function($sql) {
            return $sql->whereHas('federacoes', function ($q) {
                return $q->whereIn('sinodal_id', Auth::user()->sinodais->pluck('id')->toArray());
            });
        })
        ->when(in_array('federacao', Auth::user()->perfis->pluck('nome')->toArray()), function($sql) {
            return $sql->whereHas('locais', function ($q) {
                return $q->whereIn('federacao_id', Auth::user()->federacoes->pluck('id')->toArray());
            });
        });
        
    }
}
