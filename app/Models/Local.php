<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Local extends Model
{
    use GenericTrait, SoftDeletes;
    
    protected $table = 'locais';
    protected $guarded = ['id', 'created_at', 'updated_at'];


    public function regiao()
    {
        return $this->belongsTo(Regiao::class);
    }

    public function sinodal()
    {
        return $this->belongsTo(Sinodal::class);
    }

    public function federacao()
    {
        return $this->belongsTo(Federacao::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function usuario()
    {
        return $this->belongsToMany(User::class, 'usuario_local');
    }

    public function scopeMinhaFederacao($query)
    {
        return $query->whereIn('federacao_id', Auth::user()->federacoes->pluck('id'));
    }

}
