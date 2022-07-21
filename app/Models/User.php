<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Yajra\Acl\Traits\HasRoleAndPermission;
use Yajra\Acl\Traits\InteractsWithRole;

class User extends Authenticatable
{

    use GenericTrait;
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoleAndPermission, InteractsWithRole;
    use SoftDeletes;

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
        return $this->belongsToMany(Local::class, 'usuario_local', 'user_id', 'local_id', 'id', 'id');
    }

    public function instancia()
    {
        if ($this->hasRole('sinodal')) {
            return $this->sinodais()->first();
        } else if ($this->hasRole('federacao')) {
            return $this->federacoes()->first();
        } else if ($this->hasRole('local')) {
            return $this->locais()->first();
        }
    }

    public function scopeQuery($query)
    {
        if (Auth::user()->admin) {
            return $query;
        }

        $perfil_usuario =  Auth::user()->roles->pluck('name')->toArray();
        $param_busca = count($perfil_usuario) > 1 ? 'orWhereHas' : 'whereHas'; 
        return $query->whereDoesntHave('roles', function($sql) {
            return $sql->whereIn('name', ['diretoria']);
        })
        ->when(in_array('diretoria',$perfil_usuario), function($sql) {
            return $sql->whereHas('sinodais', function ($q) {
                return $q->whereIn('sinodais.regiao_id', Auth::user()->regioes->pluck('id')->toArray());
            })->orWhereHas('perfis', function ($q) {
                return $q->where('nome', 'secretario');
            });
        })
        ->when(in_array('sinodal',$perfil_usuario), function($sql) use ($param_busca) {
            return $sql->$param_busca('federacoes', function ($q) {
                return $q->whereIn('federacoes.sinodal_id', Auth::user()->sinodais->pluck('id')->toArray());
            });
        })
        ->when(in_array('federacao',$perfil_usuario), function($sql) use ($param_busca) {
            return $sql->$param_busca('locais', function ($q) {
                return $q->whereIn('locais.federacao_id', Auth::user()->federacoes->pluck('id')->toArray());
            });
        });
        
    }
}
