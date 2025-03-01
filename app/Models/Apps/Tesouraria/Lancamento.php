<?php

namespace App\Models\Apps\Tesouraria;

use App\Helpers\FormHelper;
use App\Models\User;
use App\Scopes\InstanciaScope;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lancamento extends Model
{
    use GenericTrait, SoftDeletes;

    protected $table = 'tesouraria_lancamentos';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Tipo entrada
     *
     * @var int
     */
    public const TIPO_ENTRADA = 1;

    /**
     * Tipo saída
     *
     * @var int
     */
    public const TIPO_SAIDA = 0;

    /**
     * Tipos de lançamento
     *
     * @var array
     */
    public const TIPOS = [
        self::TIPO_SAIDA => 'Saída',
        self::TIPO_ENTRADA => 'Entrada'
    ];


    /**
     * Scope para identificar a instancia e trazer corretamente a diretoria
     *
     * @param $query
     *
     * @return $query
     */
    public function scopeDaMinhaInstancia($query)
    {
        $role = auth()->user()->role->name;

        if ($role == User::ROLE_SINODAL) {
            $query->where('sinodal_id', auth()->user()->sinodal_id);
        }

        if ($role == User::ROLE_FEDERACAO) {
            $query->where('federacao_id', auth()->user()->federacao_id);
        }

        if ($role == User::ROLE_LOCAL) {
            $query->where('local_id', auth()->user()->local_id);
        }

        return $query;
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function getValorAttribute()
    {
        return FormHelper::converterParaReal($this->attributes['valor'] ?? 0);
    }

    protected static function booted()
    {
        static::addGlobalScope(new InstanciaScope);
    }
}
