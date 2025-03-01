<?php

namespace App\Models;

use App\Models\Apps\App;
use App\Models\Diretorias\DiretoriaFederacao;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

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
        return $this->hasOne(DiretoriaFederacao::class, 'federacao_id');
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



    protected function getDadosDatatableCacheKey(): string
    {
        return sprintf('federacao-%s-dados-datatable', $this->id);
    }

    public function clearCache(): bool
    {
        return Cache::forget($this->getDadosDatatableCacheKey());
    }

    public function getDadosDatatableAttribute(): array
    {     
        $dados = Cache::remember(
            $this->getDadosDatatableCacheKey(),
            now()->addDay(),
            function () {
                $relatorio = $this->relatorios()
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->first();

                return [
                    'estatistica' => $relatorio ? $relatorio->ano_referencia : 'Sem Relatório',
                    'regiao' => $this->regiao->nome,
                    'estado' => $this->estado->nome,
                    'sigla_sinodal' => $this->sinodal->sigla,
                    'nro_locais' => $this->locais->count()
                ];
            }
        );

        return $dados;
    }
}
