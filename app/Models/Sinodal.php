<?php

namespace App\Models;

use App\Models\Apps\App;
use App\Models\Apps\Site\Evento;
use App\Models\Apps\Site\Galeria;
use App\Models\Apps\Site\Site;
use App\Models\Estatistica\Ranking;
use App\Traits\Auditable;
use App\Traits\GenericTrait;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Sinodal extends Model
{
    use GenericTrait, SoftDeletes;

    protected $table = 'sinodais';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['data_organizacao'];

    public function regiao()
    {
        return $this->belongsTo(Regiao::class);
    }

    public function federacoes()
    {
        return $this->hasMany(Federacao::class);
    }

    public function locais()
    {
        return $this->hasMany(Local::class);
    }

    public function usuario()
    {
        return $this->belongsToMany(User::class, 'usuario_sinodal');
    }

    public function relatorios()
    {
        return $this->hasMany(FormularioSinodal::class, 'sinodal_id');
    }

    public function scopeQuery($query)
    {
        if (auth()->user()->admin) {
            return $query;
        }
        return $query->whereIn('regiao_id', auth()->user()->regioes->pluck('id')->toArray());
    }

    public function getDataOrganizacaoFormatadaAttribute()
    {
        return !is_null($this->data_organizacao) ?  $this->data_organizacao->format('d/m/Y') : 'Sem Informação';
    }

    public function ranking()
    {
        return $this->hasOne(Ranking::class, 'sinodal_id');
    }

    public function site()
    {
        return $this->hasOne(Site::class, 'sinodal_id');
    }

    public function evento()
    {
        return $this->hasOne(Evento::class, 'sinodal_id');
    }

    public function galeria()
    {
        return $this->hasMany(Galeria::class, 'sinodal_id');
    }

    public function apps()
    {
        return $this->belongsToMany(App::class, 'app_sinodal');
    }
}
