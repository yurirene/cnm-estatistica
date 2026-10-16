<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ComprovanteACI extends Model
{
    public const STATUS_APROVADO = 1;
    public const STATUS_PENDENTE = 0;
    public const STATUS_META_ATINGIDA = 2;

    public const STATUS_LABELS = [
        self::STATUS_PENDENTE => ['texto' => 'Pendente', 'cor' => 'danger'],
        self::STATUS_APROVADO => ['texto' => 'Confirmado', 'cor' => 'success'],
        self::STATUS_META_ATINGIDA => ['texto' => 'Meta atingida', 'cor' => 'info'],
    ];

    protected $table = 'comprovantes_aci';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'status' => 'integer',
    ];

    public function sinodal()
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }

    public function scopeMeusComprovantes($query)
    {
        return $query->when(auth()->user()->role->name == 'tesouraria', function($sql) {
            return $sql;
        },
        function($sql) {
            return $sql->where('sinodal_id', auth()->user()->sinodal_id);
        });
    }
}
