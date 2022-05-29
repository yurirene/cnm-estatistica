<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditable extends Model
{       
    const ACAO = [
        self::ACAO_CREATED => 'Novo Registro',
        self::ACAO_UPDATING => 'Registro Atualizado',
        self::ACAO_DELETING => 'Registro Deletado',
    ];
    const ACAO_CREATED = 'created';
    const ACAO_UPDATING = 'updating';
    const ACAO_DELETING = 'deleting';

    protected $table = 'auditable';
    protected $guarded = [];
    protected $appends = ['acao_formatada', 'data_hora', 'nome_usuario'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getAcaoFormatadaAttribute()
    {
        return key_exists($this->acao, self::ACAO) ? self::ACAO[$this->acao] : '-';
    }

    public function getDataFormatadaAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    public function getDataHoraAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    public function getNomeUsuarioAttribute()
    {
        return $this->user->name;
    }
}
