<?php

namespace App\Models;

use App\Helpers\BootstrapHelper;
use Illuminate\Database\Eloquent\Model;

class DemandaItem extends Model
{
    protected $table = 'demanda_itens';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $appends = ['nivel_formatado'];


    public const SUGESTAO = 0;
    public const RECOMENDACAO = 1;
    public const DETERMINACAO = 2;

    public const NIVEIS = [
        self::SUGESTAO => 'Sugestão',
        self::RECOMENDACAO => 'Recomendação',
        self::DETERMINACAO => 'Determinação'
    ];
    public const NIVEIS_LABELS = [
        self::SUGESTAO => 'success',
        self::RECOMENDACAO => 'warning',
        self::DETERMINACAO => 'danger'
    ];

    public const NAO_ATENDIDO = 0;
    public const ATENDIDO = 1;
    public const PENDENTE = 2;

    public const STATUS = [
        self::NAO_ATENDIDO => 'Não Atendido',
        self::ATENDIDO => 'Atendido',
        self::PENDENTE => 'Pendente'
    ];

    public const STATUS_LABELS = [
        self::NAO_ATENDIDO => 'danger',
        self::ATENDIDO => 'success',
        self::PENDENTE => 'warning'
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

