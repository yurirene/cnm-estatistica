<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Federacao extends Model
{
    use GenericTrait, SoftDeletes;
    
    protected $table = 'federacoes';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    protected $dates = ['data_organizacao'];

    protected $dates = ['data_organizacao'];

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

    public function scopeMinhaSinodal($query)
    {
        return $query->whereIn('sinodal_id', Auth::user()->sinodais->pluck('id'));
    }

    public function relatorios()
    {
        return $this->hasMany(FormularioFederacao::class, 'federacao_id');
    }

    public function getDataOrganizacaoFormatadaAttribute()
    {
        return !is_null($this->data_organizacao) ?  $this->data_organizacao->format('d/m/Y') : 'Sem Informação';
    }
}
