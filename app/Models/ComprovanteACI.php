<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ComprovanteACI extends Model
{
    protected $table = 'comprovantes_aci';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function sinodal()
    {
        return $this->belongsTo(Sinodal::class, 'sinodal_id');
    }

    public function scopeMeusComprovantes($query)
    {
        return $query->when(Auth::user()->roles->first()->name == 'tesouraria', function($sql) {
            return $sql;
        },
        function($sql) {
            return $sql->where('sinodal_id', Auth::user()->sinodais->first()->id);
        });
    }
}
