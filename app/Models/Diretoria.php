<?php

namespace App\Models;

use App\Traits\GenericTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diretoria extends Model
{
    use GenericTrait;

    protected $table = 'diretorias';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
