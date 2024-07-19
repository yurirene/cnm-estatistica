<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Secretario extends Model
{
    use GenericTrait;

    protected $table = 'secretarios';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Relacionamento one to one com a model Diretoria
     *
     * @return BelongsTo
     */
    public function diretoria(): BelongsTo
    {
        return $this->belongsTo(Diretoria::class, 'diretoria_id');
    }

}
