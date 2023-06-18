<?php

namespace App\Models\Apps\Site;

use App\Helpers\BootstrapHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoInscrito extends Model
{
    protected $table = 'evento_inscritos';
    protected $guarded = ['created_at', 'updated_at'];
    protected $casts = [
        'informacoes' => 'array'
    ];

    public const PENDENTE = 0;
    public const CONFIRMADO = 1;

    public const STATUS = [
        self::PENDENTE => 'Pendente',
        self::CONFIRMADO => 'Confirmado',
    ];

    public const STATUS_LABELS = [
        self::PENDENTE => 'danger',
        self::CONFIRMADO => 'success',
    ];

    public function getStatusFormatadoAttribute()
    {
        return self::STATUS[$this->status];
    }
}
