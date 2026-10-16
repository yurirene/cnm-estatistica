<?php

namespace App\Models\AuxilioPmf;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuxilioPmfEdital extends Model
{
    use GenericTrait;

    protected $table = 'auxilio_pmf_editais';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'data_limite_proposta' => 'date',
        'recebimento_aberto' => 'boolean',
        'ano' => 'integer',
    ];

    public function propostas(): HasMany
    {
        return $this->hasMany(AuxilioPmfProposta::class, 'edital_id');
    }

    public function estaRecebendo(): bool
    {
        return $this->recebimento_aberto
            && now()->lte($this->data_limite_proposta->copy()->endOfDay());
    }
}
