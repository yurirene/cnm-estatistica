<?php

namespace App\Models\Apps\Tesouraria;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
    protected $table = 'tesouraria_lancamentos';
    protected $guarded = ['id', 'created_at', 'updated_at'];



    /**
     * Scope para identificar a instancia e trazer corretamente a diretoria
     *
     * @param $query
     * @return $query
     */
    public function scopeDaMinhaInstancia($query)
    {
        $role = auth()->user()->roles->first()->name;
        if ($role == User::ROLE_SINODAL) {
            return $query->where('sinodal_id', auth()->user()->sinodais->first()->id);
        }

        if ($role == User::ROLE_FEDERACAO) {
            return $query->where('federacao_id', auth()->user()->federacoes->first()->id);
        }

        if ($role == User::ROLE_LOCAL) {
            return $query->where('local_id', auth()->user()->locais->first()->id);
        }

        return $query;
    }
}
