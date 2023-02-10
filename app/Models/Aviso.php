<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aviso extends Model
{
    protected $table = 'avisos';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    const LOCAL = 1;
    const FEDERACAO = 2;
    const SINODAL = 3;
    const CUSTOM = 4;
    const TIPOS = [
        self::LOCAL => 'Local',
        self::FEDERACAO => 'Federação',
        self::SINODAL => 'Sinodal',
        self::CUSTOM => 'Específico'
    ];

    public function getTipoFormatadoAttribute()
    {
        return self::TIPOS[$this->tipo];
    }

    public function locais()
    {
        return $this->belongsToMany(Local::class, 'aviso_usuarios', 'aviso_id', 'local_id');
    }

    public function federacoes()
    {
        return $this->belongsToMany(Federacao::class, 'aviso_usuarios', 'aviso_id', 'federacao_id');
    }

    public function sinodais()
    {
        return $this->belongsToMany(Sinodal::class, 'aviso_usuarios', 'aviso_id', 'sinodal_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'aviso_usuarios', 'aviso_id', 'user_id')->withPivot(['visualizado']);
    }
}
