<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;

class BotMessage extends Model
{
    use GenericTrait;

    protected $table = 'bot_messages';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function mensagem()
    {
        return $this->belongsTo(BotMessage::class, 'resposta_de');
    }
}
