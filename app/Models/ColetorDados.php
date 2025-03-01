<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColetorDados extends Model
{
    use GenericTrait;

    protected $table = 'coletor_dados';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'resposta' => 'array'
    ];

    public function local()
    {
        return $this->belongsTo(Local::class, 'local_id');
    }
}
