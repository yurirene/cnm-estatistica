<?php

namespace App\Models;

use App\Models\Apps\App;
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
        return $this->hasOne(User::class, 'federacao_id');
    }


    public function diretoria(): HasOne
    {
        return $this->hasOne(Diretoria::class, 'federacao_id');
    }

    public function relatorios()
    {
        return $this->hasMany(FormularioFederacao::class, 'federacao_id');
    }

    public function apps()
    {
        return $this->belongsToMany(App::class, 'app_federacao');
    }

    public function scopeMinhaSinodal($query)
    {
        return $query->where('sinodal_id', auth()->user()->sinodal_id);
    }

    public function scopeDaMinhaRegiao($query)
    {
        return $query->where('regiao_id', auth()->user()->regiao_id);
    }
}
