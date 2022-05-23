<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use Uuid;
    
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

}
