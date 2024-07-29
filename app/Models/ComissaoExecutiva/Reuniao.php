<?php

namespace App\Models\ComissaoExecutiva;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reuniao extends Model
{
    use GenericTrait;

    protected $table = 'comissao_executiva_reunioes';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoRecebido::class, 'reuniao_id');
    }
}
