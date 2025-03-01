<?php

namespace App\Models;

use App\Traits\CacheTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasPermission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property string name
 * @property string slug
 * @property string description
 * @property bool system
 */
class Role extends Model
{
    use CacheTrait;
    /** @var string */
    protected $table = 'roles';

    /** @var array */
    protected $fillable = ['name', 'slug', 'description', 'system'];

    /** @var array */
    protected $casts = [
        'system' => 'bool',
    ];
    
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    // Define the cache key (as its used in multiple places)
    protected function getPermissionsCacheKey(): string
    {
        return sprintf('user-%d-permissions', $this->id);
    }

    // Provide a cache clearing mechanism
    public function clearCache(): bool
    {
        return Cache::forget($this->getPermissionsCacheKey());
    }

    // Override the relation property getter
    // It will return the cached collection when it exists, otherwise getting a fresh one from the database
    // It then populates the relation with that collection for use elsewhere
    public function getPermissionsAttribute(): Collection
    {
        // If the relation is already loaded and set to the current instance of model, return it
        if ($this->relationLoaded('permissions')) {
            return $this->getRelationValue('permissions');
        }

        // Get the relation from the cache, or load it from the datasource and set to the cache
        $permissions = Cache::rememberForever($this->getPermissionsCacheKey(), function () {
            return $this->getRelationValue('permissions');
        });

        // Set the relation to the current instance of model
        $this->setRelation('permissions', $permissions);

        return $permissions;
    }
}
