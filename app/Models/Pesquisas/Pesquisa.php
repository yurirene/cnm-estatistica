<?php

namespace App\Models\Pesquisas;

use App\Models\User;
use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Model;

class Pesquisa extends Model
{
    use GenericTrait;

    protected $table = 'pesquisas';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = ['referencias' => 'array', 'instancias' => 'array'];

    public const INSTANCIAS = [
        'Sinodal' => 'Sinodal',
        'Federação' => 'Federação',
        'Local' => 'Local'
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'pesquisa_visualizacao_usuario');
    }

    public function respostas()
    {
        return $this->hasMany(PesquisaResposta::class);
    }

    public function configuracao()
    {
        return $this->hasOne(PesquisaConfiguracao::class);
    }
}
