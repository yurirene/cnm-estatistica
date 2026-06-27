<?php

namespace App\Models\Diretorias;

use App\Casts\Encryptable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiretoriaFederacao extends Model
{
    protected $table = 'diretorias_federacao';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'presidente' => Encryptable::class,
        'vice_presidente' => Encryptable::class,
        'secretario_executivo' => Encryptable::class,
        'primeiro_secretario' => Encryptable::class,
        'segundo_secretario' => Encryptable::class,
        'tesoureiro' => Encryptable::class,
        'secretario_presbiterial' => Encryptable::class,

        'contato_presidente' => Encryptable::class,
        'contato_vice_presidente' => Encryptable::class,
        'contato_secretario_executivo' => Encryptable::class,
        'contato_primeiro_secretario' => Encryptable::class,
        'contato_segundo_secretario' => Encryptable::class,
        'contato_tesoureiro' => Encryptable::class,
        'contato_secretario_presbiterialapp/Models/Diretorias/DiretoriaFederacao.php' => Encryptable::class,

        'secretarios' => 'array',
        'updated_at' => 'date',
        'created_at' => 'date'
    ];
}
