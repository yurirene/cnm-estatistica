<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Perfil extends Model
{
    protected $table = 'perfis';
    protected $guarded = ['id', 'created_at', 'updated_at'];

}
