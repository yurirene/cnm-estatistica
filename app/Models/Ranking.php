<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    protected $table = 'rankings';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    public function getExplicacaoDetalhadaAttribute()
    {
        $texto_federacao = $this->form_fed_entregue > 1 ? 'formulários de Federações' : 'formulário de Federação'; 
        $texto_local = $this->form_local_entregue > 1 ? 'formulários de UMPs Locais' : 'formulário de UMP Local';
        return "Sua posição é resultado da entrega de {$this->form_fed_entregue} {$texto_federacao} 
        e {$this->form_local_entregue} {$texto_local}. Total de Pontos: {$this->pontuacao} de 100";
    }
}
