<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estados';
    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     * Scope query para filtrar só pela regiao do usuário
     *
     * @param [type] $query
     * @return void
     */
    public function scopeDaMinhaRegiao($query)
    {
        return $query->whereIn('regiao_id', auth()->user()->regioes->pluck('id'));
    }
}
