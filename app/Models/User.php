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

    public function local()
    {
        return $this->belongsToMany(Local::class, 'usuario_local');
    }

    public function scopeQuery($query)
    {
        if (Auth::user()->admin) {
            return $query;
        }
        return $query;
    }
}
