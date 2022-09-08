<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demanda extends Model
{
    protected $table = 'demandas';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function itens()
    {
        return $this->hasMany(DemandaItem::class, 'demanda_id');
    }
}
