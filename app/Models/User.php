<?php

namespace App\Models;

use App\Models\Pesquisas\Pesquisa;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    public const ROLE_TESOURARIA = 'tesouraria';
    public const ROLE_ADMINISTRADOR = 'administrador';
    public const ROLE_SEC_EXECUTIVA = 'executiva';

    public function regioes(): BelongsTo
    {
        return $this->belongsTo(Regiao::class, 'regiao_id');
    }

    public function atividades()
    {
        return $this->hasMany(Atividade::class);
    }

    public function sinodal(): BelongsTo
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }

    public function federacao(): BelongsTo
    {
        return $this->belongsTo(Federacao::class, 'federacao_id');
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'local_id');
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
            $relation = $this->sinodal();
        } elseif ($this->hasRole(self::ROLE_FEDERACAO)) {
            $relation = $this->federacao();
        } elseif ($this->hasRole(self::ROLE_LOCAL)) {
            $relation = $this->local();
        }
        return $relation;
    }

    public function scopeQuery($query)
    {
        if (auth()->user()->admin) {
            return $query;
        }

        $perfil_usuario =  Auth::user()->roles->pluck('name')->toArray();
        $param_busca = count($perfil_usuario) > 1 ? 'orWhereHas' : 'whereHas';
        return $query->whereDoesntHave('roles', function($sql) {
            return $sql->whereIn('name', ['diretoria']);
        })
        ->when(in_array('diretoria', $perfil_usuario), function($sql) {
            return $sql->whereHas('sinodais', function ($q) {
                return $q->where('sinodais.regiao_id', auth()->user()->regiao_id);
            })->orWhereHas('roles', function ($q) {
                return $q->whereIn('name', self::ROLES_SECRETARIOS);
            });
        })
        ->when(in_array('sinodal', $perfil_usuario), function($sql) use ($param_busca) {
            return $sql->$param_busca('federacoes', function ($q) {
                return $q->where('federacoes.sinodal_id', auth()->user()->sinodal_id);
            });
        })
        ->when(in_array('federacao', $perfil_usuario), function($sql) use ($param_busca) {
            return $sql->$param_busca('locais', function ($q) {
                return $q->where('locais.federacao_id', auth()->user()->federacao_id);
            });
        });

    }

    public function getInstanciaFormatadaAttribute()
    {
        $instancia = '';
        if ($this->roles->first()->name == self::ROLE_ADMINISTRADOR) {
            $instancia = 'Administrador';
        } elseif ($this->roles->first()->name == self::ROLE_DIRETORIA) {
            $instancia = 'Diretoria';
        } elseif ($this->roles->first()->name == self::ROLE_SINODAL) {
            $instancia = 'Sinodal';
        } elseif ($this->roles->first()->name == self::ROLE_FEDERACAO) {
            $instancia = 'Federação';
        } elseif ($this->roles->first()->name == self::ROLE_LOCAL) {
            $instancia = 'Local';
        }
        return $instancia;
    }

    public function avisos()
    {
        return $this->belongsToMany(Aviso::class, 'aviso_usuarios', 'user_id', 'aviso_id')
            ->withPivot('visualizado');
    }

}
