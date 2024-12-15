<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Local extends Model
{
    use GenericTrait, SoftDeletes;

    protected $table = 'locais';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $dates = ['data_organizacao'];

    public function regiao()
    {
        return $this->belongsTo(Regiao::class);
    }

    public function sinodal()
    {
        return $this->belongsTo(Sinodal::class);
    }

    public function federacao()
    {
        return $this->belongsTo(Federacao::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function usuario()
    {
        return $this->hasOne(User::class, 'local_id');
    }

    public function relatorios()
    {
        return $this->hasMany(FormularioLocal::class, 'local_id');
    }

    public function scopeMinhaFederacao($query)
    {
        return $query->where('federacao_id', auth()->user()->federacao_id);
    }

    public function getDataOrganizacaoFormatadaAttribute()
    {
        return !is_null($this->data_organizacao) ?  $this->data_organizacao->format('d/m/Y') : 'Sem Informação';
    }

    public function scopeDaMinhaRegiao($query)
    {
        return $query->where('regiao_id', auth()->user()->regiao_id);
    }

    public function scopeMinhaSinodal($query)
    {
        return $query->where('sinodal_id', auth()->user()->sinodal_id);
    }
}
