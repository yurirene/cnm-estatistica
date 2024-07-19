<?php

namespace App\Scopes;

use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class InstanciaScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $instancia = UserService::getCampoInstanciaDB();

        $builder->where($instancia['campo'], $instancia['id']);
    }
}
