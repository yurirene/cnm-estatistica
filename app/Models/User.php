<?php

namespace App\Models;

use App\Models\Pesquisas\Pesquisa;
use App\Traits\CacheTrait;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use GenericTrait;
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'local_id',
        'federacao_id',
        'sinodal_id',
        'regiao_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public const ROLES_SECRETARIOS = [
        'secreatria_produtos',
        'secreatariado_comum',
        'secretaria_estatistica'
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

    public function regiao(): BelongsTo
    {
        return $this->belongsTo(Regiao::class, 'regiao_id');
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
        return $this->role;
    }

    public function can($ability, $arguments = [])
    {
        // Se for uma string simples, verifica se é o nome do role
        if (is_string($ability) && empty($arguments)) {
            return $this->role->name === $ability;
        }
        
        // Chama o método da classe pai para outras verificações
        return parent::can($ability, $arguments);
    }

    public function instancia()
    {
        $relation = null;

        if ($this->can(self::ROLE_SINODAL)) {
            $relation = $this->sinodal;
        } elseif ($this->can(self::ROLE_FEDERACAO)) {
            $relation = $this->federacao;
        } elseif ($this->can(self::ROLE_LOCAL)) {
            $relation = $this->local;
        }

        return $relation;
    }

    public function scopeQuery($query)
    {
        if (auth()->user()->admin) {
            return $query;
        }

        $perfil_usuario =  auth()->user()->role->toArray();
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
        if ($this->role->name == self::ROLE_ADMINISTRADOR) {
            $instancia = 'Administrador';
        } elseif ($this->role->name == self::ROLE_DIRETORIA) {
            $instancia = 'Diretoria';
        } elseif ($this->role->name == self::ROLE_SINODAL) {
            $instancia = 'Sinodal';
        } elseif ($this->role->name == self::ROLE_FEDERACAO) {
            $instancia = 'Federação';
        } elseif ($this->role->name == self::ROLE_LOCAL) {
            $instancia = 'Local';
        }
        return $instancia;
    }

    public function avisos()
    {
        return $this->belongsToMany(Aviso::class, 'aviso_usuarios', 'user_id', 'aviso_id')
            ->withPivot('visualizado');
    }
    
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Define the cache key (as its used in multiple places)
    protected function getRoleCacheKey(): string
    {
        return sprintf('user-%s-role', $this->id);
    }

    // Provide a cache clearing mechanism
    public function clearCache(): bool
    {
        return Cache::forget($this->getRoleCacheKey());
    }

    // Override the relation property getter
    // It will return the cached collection when it exists, otherwise getting a fresh one from the database
    // It then populates the relation with that collection for use elsewhere
    public function getRoleAttribute(): Role
    {
        // If the relation is already loaded and set to the current instance of model, return it
        if ($this->relationLoaded('role')) {
            return $this->getRelationValue('role');
        }

        // Get the relation from the cache, or load it from the datasource and set to the cache
        $role = Cache::rememberForever($this->getRoleCacheKey(), function () {
            return $this->getRelationValue('role');
        });

        // Set the relation to the current instance of model
        $this->setRelation('role', $role);

        return $role;
    }
}
