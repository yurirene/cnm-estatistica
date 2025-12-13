<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferenciaUnidade extends Model
{
    use HasFactory;
    protected $table = 'transferencia_unidades';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function federacao()
    {
        return $this->belongsTo(Federacao::class, 'federacao_id');
    }

    public function sinodal()
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'local_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
