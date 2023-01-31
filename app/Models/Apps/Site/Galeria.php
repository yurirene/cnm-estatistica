<?php

namespace App\Models\Apps\Site;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeria extends Model
{
    protected $table = 'sites_galerias';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
