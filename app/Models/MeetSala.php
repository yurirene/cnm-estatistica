<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetSala extends Model
{
    use GenericTrait;

    protected $table = 'meet_salas';

    protected $fillable = [
        'user_id',
        'para_user_id',
        'titulo',
        'inicia_em',
        'termina_em',
        'meet_name',
        'meet_uri',
        'meet_code',
    ];

    protected $casts = [
        'inicia_em' => 'datetime',
        'termina_em' => 'datetime',
    ];

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function para(): BelongsTo
    {
        return $this->belongsTo(User::class, 'para_user_id');
    }
}
