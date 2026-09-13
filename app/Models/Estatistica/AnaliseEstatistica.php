<?php

namespace App\Models\Estatistica;

use App\Services\EstatisticaInteligente\Enums\NivelEstatisticoEnum;
use App\Services\EstatisticaInteligente\Enums\StatusAnaliseEnum;
use App\Services\EstatisticaInteligente\Enums\TipoAnaliseEnum;
use Illuminate\Database\Eloquent\Model;

class AnaliseEstatistica extends Model
{
    protected $table = 'analises_estatisticas';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'conteudo' => 'array',
        'dados_contexto_json' => 'array',
        'gerado_em' => 'datetime',
        'ano_referencia' => 'integer',
    ];

    public function nivelEnum(): NivelEstatisticoEnum
    {
        return NivelEstatisticoEnum::from($this->nivel);
    }

    public function tipoEnum(): TipoAnaliseEnum
    {
        return TipoAnaliseEnum::from($this->tipo);
    }

    public function statusEnum(): StatusAnaliseEnum
    {
        return StatusAnaliseEnum::from($this->status);
    }
}
