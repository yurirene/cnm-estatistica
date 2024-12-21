<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @property string resource
 * @property string name
 * @property string slug
 * @property bool system
 */
class Permission extends Model
{
    /** @var string */
    protected $table = 'permissions';

    /** @var array */
    protected $fillable = ['name', 'slug', 'resource', 'system'];

    /** @var array */
    protected $casts = ['system' => 'bool'];
}
