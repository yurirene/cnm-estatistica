<?php

namespace App\Policies;

use App\Models\Gamificacao\Placar;
use App\Models\User;

class PlacarPolicy
{
    public function view(User $user, Placar $placar): bool
    {
        if ($user->admin) {
            return true;
        }

        if (($user->role->name ?? null) === User::ROLE_SINODAL) {
            return $placar->sinodal_id !== null && $placar->sinodal_id === $user->sinodal_id;
        }

        if (($user->role->name ?? null) === User::ROLE_FEDERACAO) {
            return $placar->federacao_id !== null && $placar->federacao_id === $user->federacao_id;
        }

        return false;
    }

    public function update(User $user, Placar $placar): bool
    {
        return false;
    }

    public function delete(User $user, Placar $placar): bool
    {
        return false;
    }
}
