<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;

class BotEnvios extends Model
{
    protected $table = 'bot_envios';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function mensagem()
    {
        return $this->belongsTo(BotMessage::class, 'mensagem_servidor');
    }
}
