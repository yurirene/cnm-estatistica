<?php

namespace App\Models\Gamificacao;

use App\Models\Federacao;
use App\Models\Local;
use App\Models\Sinodal;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VinculoExercicio extends Model
{
    use GenericTrait;

    protected $table = 'gamificacao_vinculos_exercicio';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'ano_referencia' => 'integer',
    ];

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'local_id');
    }

    public function federacao(): BelongsTo
    {
        return $this->belongsTo(Federacao::class, 'federacao_id');
    }

    public function sinodal(): BelongsTo
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }
}
