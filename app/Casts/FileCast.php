<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Storage;

class FileCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return str_replace('public', 'storage', $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (empty($value)) {
            return $value;
        }

        if (!empty($attributes[$key])) {
            try {
                Storage::delete($model->getRawOriginal($key));
            } catch (\Throwable $th) {}
        }

        $filename = $key . '_' . time() . '.' . $value->getClientOriginalExtension();

        // $model->caminho tem que ser uma propriedade publica
        // dentro da model contendo os caminho que será salvo
        return $value->storeAs($model->caminho, $filename);
    }
}
