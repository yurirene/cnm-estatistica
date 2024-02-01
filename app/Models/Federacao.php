<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Federacao extends Model
{
    use GenericTrait, SoftDeletes;

    protected $table = 'federacoes';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $dates = ['data_organizacao'];


    public function getDataOrganizacaoFormatadaAttribute()
    {
        return !is_null($this->data_organizacao) ?  $this->data_organizacao->format('d/m/Y') : 'Sem Informação';
    }

    public function regiao()
    {
        return $this->belongsTo(Regiao::class);
    }

    public function sinodal()
    {
        return $this->belongsTo(Sinodal::class);
    }


    public function locais()
    {
        return $this->hasMany(Local::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function usuario()
    {
        return $this->belongsToMany(User::class, 'usuario_federacao');
    }


    public function diretoria(): HasOne
    {
        return $this->hasOne(Diretoria::class, 'federacao_id');
    }

    public function relatorios()
    {
        return $this->hasMany(FormularioFederacao::class, 'federacao_id');
    }

    public function scopeMinhaSinodal($query)
    {
        return $query->whereIn('sinodal_id', auth()->user()->sinodais->pluck('id'));
    }

    public function scopeDaMinhaRegiao($query)
    {
        return $query->whereIn('regiao_id', auth()->user()->regioes->pluck('id'));
    }
}
