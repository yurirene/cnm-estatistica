<?php

namespace App\Models\Diretorias;

use App\Casts\Encryptable;
use Illuminate\Database\Eloquent\Model;

class DiretoriaSinodal extends Model
{
    protected $table = 'diretorias_sinodal';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'presidente' => Encryptable::class,
        'vice_presidente' => Encryptable::class,
        'secretario_executivo' => Encryptable::class,
        'primeiro_secretario' => Encryptable::class,
        'segundo_secretario' => Encryptable::class,
        'tesoureiro' => Encryptable::class,
        'secretario_sinodal' => Encryptable::class,

        'contato_presidente' => Encryptable::class,
        'contato_vice_presidente' => Encryptable::class,
        'contato_secretario_executivo' => Encryptable::class,
        'contato_primeiro_secretario' => Encryptable::class,
        'contato_segundo_secretario' => Encryptable::class,
        'contato_tesoureiro' => Encryptable::class,
        'contato_secretario_sinodal' => Encryptable::class,

        'secretarios' => 'array',
        'updated_at' => 'date',
        'created_at' => 'date'
    ];

}
