<?php

namespace App\Models;

use App\Models\Apps\App;
use App\Models\Apps\Site\Evento;
use App\Models\Apps\Site\Galeria;
use App\Models\Apps\Site\Site;
use App\Models\Diretorias\DiretoriaSinodal;
use App\Models\Estatistica\Ranking;

use App\Traits\GenericTrait;
use App\Traits\Uuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Sinodal extends Model
{
    use GenericTrait, SoftDeletes;

    protected $table = 'sinodais';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'data_organizacao' => 'date'
    ];

    public function getDataOrganizacaoFormatadaAttribute()
    {
        if (is_null($this->data_organizacao)) {
            return 'Sem Informação';
        }

        if (is_string($this->data_organizacao)) {
            return Carbon::parse($this->data_organizacao)->format('d/m/Y');
        }

        if ($this->data_organizacao instanceof Carbon) {
            return $this->data_organizacao->format('d/m/Y');
        }

        return 'Sem Informação';
    }

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
        return $this->hasOne(User::class, 'sinodal_id');
    }

    public function relatorios()
    {
        return $this->hasMany(FormularioSinodal::class, 'sinodal_id');
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

    public function scopeQuery($query)
    {
        if (auth()->user()->admin) {
            return $query;
        }
        return $query->where('regiao_id', auth()->user()->regiao_id);
    }

    public function diretoria()
    {
        return $this->hasOne(DiretoriaSinodal::class, 'sinodal_id');
    }

    protected function getDadosFederacaoLocalCacheKey(): string
    {
        return sprintf('sinodal-%s-dados-federacao-local', $this->id);
    }

    public function clearCache(): bool
    {
        return Cache::forget($this->getDadosFederacaoLocalCacheKey());
    }

    public function getDadosFederacaoLocalAttribute(): array
    {
        $dados = Cache::remember(
            $this->getDadosFederacaoLocalCacheKey(),
            now()->addDay(),
            function () {
                return [
                    'regiao' => $this->regiao->nome,
                    'nro_federacoes' => $this->federacoes->count(),
                    'nro_locais' => $this->locais->count()
                ];
            }
        );

        return $dados;
    }

    public function formularioComplementar()
    {
        return $this->hasOne(FormularioComplementarSinodal::class, 'sinodal_id');
    }
}
