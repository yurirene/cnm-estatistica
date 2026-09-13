<?php

namespace App\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GamificacaoInstanciaScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! auth()->check()) {
            return;
        }

        $usuario = auth()->user();
        $perfil = $usuario->role->name ?? null;

        if ($perfil === User::ROLE_SINODAL && $usuario->sinodal_id) {
            $builder->where('sinodal_id', $usuario->sinodal_id);
            return;
        }

        if ($perfil === User::ROLE_FEDERACAO && $usuario->federacao_id) {
            $builder->where('federacao_id', $usuario->federacao_id);
        }
    }
}
