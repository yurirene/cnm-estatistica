<?php

namespace App\Traits;

trait CacheTrait
{
   /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            if (method_exists($model, 'clearCache')) {
                $model->clearCache();
            }
        });
    }
}
