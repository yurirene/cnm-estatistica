<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GenericTrait
{
   /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        //-----GERA O UUID PARA AS TABELAS-----

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });


        static::updating(function ($model) {
            if (method_exists($model, 'clearCache')) {
                $model->clearCache();
            }
        });
    }

   /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

   /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
