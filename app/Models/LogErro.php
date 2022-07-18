<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogErro extends Model
{
    protected $table = 'log_erros';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'log' => 'array'
    ];

    public function usuario()
    {
        return $this->hasOne(User::class);
    }
}
