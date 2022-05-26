<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Federacao extends Model
{
    use Uuid;
    
    protected $table = 'federacoes';
    protected $guarded = ['id', 'created_at', 'updated_at'];


    public function regiao()
    {
        return $this->belongsTo(Regiao::class);
    }

    public function sinodal()
    {
        return $this->belongsTo(Sinodal::class);
    }


    public function locais()
    {
        return $this->hasMany(Local::class);
    }
    
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function usuario()
    {
        return $this->belongsToMany(User::class, 'usuario_federacao');
    }
}
