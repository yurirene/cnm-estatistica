<?php

namespace App\Models\Apps\Site;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $table = 'sites';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
