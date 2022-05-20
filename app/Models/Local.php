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

    public function locais()
    {
        return $this->hasMany(Local::class);
    }
}
