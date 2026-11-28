<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'status' => true,
            'admin' => false,
            'must_change_password' => false,
            'role_id' => Role::query()->firstOrCreate(
                ['name' => User::ROLE_LOCAL],
                ['slug' => User::ROLE_LOCAL, 'description' => User::ROLE_LOCAL, 'system' => false]
            )->id,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    public function admin(): static
    {
        return $this->state(function () {
            $role = Role::query()->firstOrCreate(
                ['name' => User::ROLE_ADMINISTRADOR],
                ['slug' => User::ROLE_ADMINISTRADOR, 'description' => 'Admin', 'system' => true]
            );

            return [
                'admin' => true,
                'role_id' => $role->id,
            ];
        });
    }

    public function diretoria(): static
    {
        return $this->state(function () {
            $role = Role::query()->firstOrCreate(
                ['name' => User::ROLE_DIRETORIA],
                ['slug' => User::ROLE_DIRETORIA, 'description' => 'Diretoria', 'system' => false]
            );

            return ['role_id' => $role->id];
        });
    }
}
