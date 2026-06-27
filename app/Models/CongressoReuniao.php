<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CongressoReuniao extends Model
{
    use HasFactory;

    protected $table = 'congresso_reunioes';

    protected $fillable = ['nome', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeAberta(Builder $query): Builder
    {
        return $query->where('status', true);
    }
}
