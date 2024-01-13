<?php

namespace App\Models;

use App\Models\Pesquisas\Pesquisa;
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

    public const ROLES_SECRETARIOS = [
        'secretaria_eventos',
        'secreatria_produtos',
        'secretaria_evangelismo',
        'secretaria_responsabilidade'
    ];


    public const ROLES_INSTANCIAS = [
        self::ROLE_SINODAL,
        self::ROLE_FEDERACAO,
        self::ROLE_LOCAL
    ];

    public const ROLE_DIRETORIA = 'diretoria';
    public const ROLE_SINODAL = 'sinodal';
    public const ROLE_FEDERACAO = 'federacao';
    public const ROLE_LOCAL = 'local';
    public const ROLE_ADMINISTRADOR = 'administrador';

    public function regioes()
    {
        return $this->belongsToMany(Regiao::class, 'usuario_regiao');
    }

    public function atividades()
    {
        return $this->hasMany(Atividade::class);
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

    public function pesquisas()
    {
        return $this->belongsToMany(Pesquisa::class, 'pesquisa_respostas');
    }

    public function perfil()
    {
        return $this->roles->first();
    }

    public function instancia()
    {
        if ($this->hasRole(self::ROLE_SINODAL)) {
            return $this->sinodais();
        } elseif ($this->hasRole(self::ROLE_FEDERACAO)) {
            return $this->federacoes();
        } elseif ($this->hasRole(self::ROLE_LOCAL)) {
            return $this->locais();
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
                return $q->whereIn('sinodais.regiao_id', auth()->user()->regioes->pluck('id')->toArray());
            })->orWhereHas('roles', function ($q) {
                return $q->whereIn('name', self::ROLES_SECRETARIOS);
            });
        })
        ->when(in_array('sinodal',$perfil_usuario), function($sql) use ($param_busca) {
            return $sql->$param_busca('federacoes', function ($q) {
                return $q->whereIn('federacoes.sinodal_id', auth()->user()->sinodais->pluck('id')->toArray());
            });
        })
        ->when(in_array('federacao',$perfil_usuario), function($sql) use ($param_busca) {
            return $sql->$param_busca('locais', function ($q) {
                return $q->whereIn('locais.federacao_id', auth()->user()->federacoes->pluck('id')->toArray());
            });
        });

    }

    public function getInstanciaFormatadaAttribute()
    {
        if ($this->roles->first()->name == self::ROLE_ADMINISTRADOR) {
            return 'Administrador';
        } elseif ($this->roles->first()->name == self::ROLE_DIRETORIA) {
            return 'Diretoria';
        } elseif ($this->roles->first()->name == self::ROLE_SINODAL) {
            return 'Sinodal';
        } elseif ($this->roles->first()->name == self::ROLE_FEDERACAO) {
            return 'Federação';
        } elseif ($this->roles->first()->name == self::ROLE_LOCAL) {
            return 'Local';
        }
    }

    public function avisos()
    {
        return $this->belongsToMany(Aviso::class, 'aviso_usuarios', 'user_id', 'aviso_id')
            ->withPivot('visualizado');
    }

}
