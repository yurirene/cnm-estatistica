<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sugestao extends Model
{

    protected $table = 'sugestoes';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $dates = ['created_at'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeQuery($query)
    {
        if (auth()->user()->admin) {
            return $query;
        }
        return $query->where('user_id', auth()->id());
    }
}
