<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprovanteACI extends Model
{
    protected $table = 'comprovantes_aci';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function sinodal()
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }
}
