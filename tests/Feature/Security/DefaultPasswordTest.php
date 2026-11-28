<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesUsersAndRoles;
use Tests\TestCase;

class DefaultPasswordTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsersAndRoles;

    public function test_store_uses_password_123_and_forces_change(): void
    {
        $role = $this->createRole(User::ROLE_LOCAL);

        $usuario = UserService::store(new Request([
            'name' => 'Novo Usuário',
            'email' => 'novo@example.test',
            'role_id' => $role->id,
        ]));

        $this->assertTrue(Hash::check('123', $usuario->fresh()->password));
        $this->assertTrue($usuario->fresh()->must_change_password);
        $this->assertSame('123', $usuario->temporary_plain_password);
    }

    public function test_reset_uses_password_123_and_forces_change(): void
    {
        $usuario = $this->createUserWithRole(User::ROLE_LOCAL, [
            'password' => Hash::make('antiga'),
        ]);

        $senha = UserService::resetarSenha($usuario);

        $this->assertSame('123', $senha);
        $this->assertTrue(Hash::check('123', $usuario->fresh()->password));
        $this->assertTrue($usuario->fresh()->must_change_password);
    }

    public function test_get_reset_route_no_longer_exists(): void
    {
        $usuario = $this->createUserWithRole(User::ROLE_ADMINISTRADOR);

        $this->actingAs($usuario)
            ->get('/dashboard/usuarios-senha-resetar/' . $usuario->id)
            ->assertStatus(404);
    }

    public function test_user_with_must_change_password_is_blocked_from_other_routes(): void
    {
        $usuario = $this->createUserWithRole(User::ROLE_LOCAL, [
            'must_change_password' => true,
        ], ['dashboard.locais.index']);

        $this->actingAs($usuario)
            ->get(route('dashboard.locais.index'))
            ->assertRedirect(route('dashboard.home'));
    }
}
