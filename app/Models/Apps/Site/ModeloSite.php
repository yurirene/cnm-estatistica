<?php

namespace App\Models\Apps\Site;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModeloSite extends Model
{
    protected $table = 'sites_modelos';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'configuracoes' => 'array',
        'mapeamento' => 'array'
    ];
}
