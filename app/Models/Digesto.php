<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Digesto extends Model
{
    protected $table = 'digestos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $appends = ['tipo_formatado', 'texto_selecionado'];

    public function tipo()
    {
        return $this->belongsTo(TipoReuniao::class, 'tipo_reuniao_id');
    }

    public function getTipoFormatadoAttribute()
    {
        return $this->tipo->nome;
    }

    public function getTextoSelecionadoAttribute()
    {
        if (request()->filled('chave')) {
            $inicio = strpos($this->texto, request()->chave);
            return substr($this->texto, $inicio, 40);
        }
        return '';
    }

}
