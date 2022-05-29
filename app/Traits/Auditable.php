<?php

namespace App\Traits;


use App\Services\AuditableService;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function boot()
    {
        parent::boot();

        // create a event to happen on updating
        static::updating(function ($table) {
            AuditableService::store(
                $table,
                Auth::id() ?? null,
                'updating'
            );
        });

        // create a event to happen on saving
        static::created(function ($table) {
            AuditableService::store(
                $table,
                Auth::id() ?? null,
                'created'
            );
        });

        // create a event to happen on deleting
        static::deleting(function ($table) {
            AuditableService::store(
                $table,
                Auth::id() ?? null,
                'deleting'
            );
        });
    }
}
