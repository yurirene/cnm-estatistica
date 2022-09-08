<?php

namespace App\Models;

use App\Helpers\BootstrapHelper;
use Illuminate\Database\Eloquent\Model;

class DemandaItem extends Model
{
    protected $table = 'demanda_itens';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $appends = ['nivel_formatado'];

    public const NIVEIS = [
        0 => 'Sugestão',
        1 => 'Recomendação',
        3 => 'Determinação'
    ];
    public const NIVEIS_LABELS = [
        0 => 'success',
        1 => 'warning',
        3 => 'danger'
    ];

    public const STATUS = [
        0 => 'Não Atendido',
        1 => 'Atendido',
        3 => 'Pendente'
    ];

    public const STATUS_LABELS = [
        0 => 'danger',
        1 => 'success',
        3 => 'warning'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getNivelFormatadoAttribute()
    {
        if (!isset(self::NIVEIS[$this->nivel])) {
            return '';
        }
        return BootstrapHelper::badge(self::NIVEIS_LABELS[$this->nivel] , self::NIVEIS[$this->nivel]);
    }
}

