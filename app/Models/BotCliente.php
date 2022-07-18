<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;

class BotCliente extends Model
{
    use GenericTrait;

    protected $table = 'bot_clientes';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
