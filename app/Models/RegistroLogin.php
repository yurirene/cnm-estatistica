<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroLogin extends Model
{
    use Uuid;
    
    protected $table = 'registro_logins';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
