<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;

class Diretoria extends Model
{
    use GenericTrait;

    protected $table = 'diretorias';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public const LABELS = [
        'presidente' => 'Presidente',
        'vice_presidente' => 'Vice-Presidente',
        'primeiro_secretario' => 'Primeiro Secretário',
        'segundo_secretario' => 'Segundo Secretário',
        'secretario_executivo' => 'Secretário-Executivo',
        'tesoureiro' => 'Tesoureiro',
        'secretario_causas' => 'Secretário Causas'
    ];

    public const IMAGEM_PADRAO = 'img/team-1.jpg';

    public function informacoes()
    {
        return $this->hasOne(DiretoriaInformacao::class, 'diretoria_id');
    }

    public function secretarios()
    {
        return $this->hasMany(Secretario::class, 'diretoria_id');
    }

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
