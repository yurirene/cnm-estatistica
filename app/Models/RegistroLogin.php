<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroLogin extends Model
{    
    protected $table = 'registro_logins';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
