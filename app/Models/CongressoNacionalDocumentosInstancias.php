<?php

namespace App\Models;

use App\Models\CongressoNacional\DelegadoCongressoNacional;
use App\Models\CongressoReuniao;
use App\Models\Federacao;
use App\Models\Sinodal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CongressoNacionalDocumentosInstancias extends Model
{
    use HasFactory;

    protected $table = 'congresso_nacional_documentos_instancias';

    protected $fillable = [
        'federacao_id',
        'sinodal_id',
        'diretoria',
        'estatistico',
        'planejamento',
        'status',
        'reuniao_id',
    ];

    protected $casts = [
        'diretoria' => 'boolean',
        'estatistico' => 'boolean',
        'planejamento' => 'boolean',
        'status' => 'boolean',
    ];

    public function federacao(): BelongsTo
    {
        return $this->belongsTo(Federacao::class, 'federacao_id');
    }

    public function sinodal(): BelongsTo
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }

    public function reuniao(): BelongsTo
    {
        return $this->belongsTo(CongressoReuniao::class, 'reuniao_id');
    }

    /**
     * Retorna um delegado desta instância (para gerar links de exportação).
     */
    public function getDelegadoParaExportacaoAttribute(): ?DelegadoCongressoNacional
    {
        $query = DelegadoCongressoNacional::query()
            ->where('sinodal_id', $this->sinodal_id)
            ->where('reuniao_id', $this->reuniao_id);

        if ($this->federacao_id) {
            $query->where('federacao_id', $this->federacao_id);
        } else {
            $query->whereNull('federacao_id');
        }

        return $query->first();
    }

    public function getDelegadosCredenciadosAttribute(): Collection
    {
        public function getTelefonesCredenciadosAttribute(): string
    {
        $query = DelegadoCongressoNacional::query()
            ->where('sinodal_id', $this->sinodal_id)
            ->where('reuniao_id', $this->reuniao_id);

        if ($this->federacao_id) {
            $query->where('federacao_id', $this->federacao_id);
        } else {
            $query->whereNull('federacao_id');
        }

        $delegado = $query->where('credencial', true)
            ->where('pago', true)
            ->get()
            ->pluck('telefone');


        return $delegados->implode(', ');
    }
}
