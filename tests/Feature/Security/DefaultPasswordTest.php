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

    public function test_store_does_not_use_password_123(): void
    {
        $role = $this->createRole(User::ROLE_LOCAL);

        $usuario = UserService::store(new Request([
            'name' => 'Novo Usuário',
            'email' => 'novo@example.test',
            'role_id' => $role->id,
        ]));

        $this->assertFalse(Hash::check('123', $usuario->password));
        $this->assertTrue($usuario->must_change_password);
        $this->assertNotEmpty($usuario->temporary_plain_password);
        $this->assertTrue(Hash::check($usuario->temporary_plain_password, $usuario->fresh()->password));
    }

    public function test_reset_does_not_use_password_123(): void
    {
        $usuario = $this->createUserWithRole(User::ROLE_LOCAL, [
            'password' => Hash::make('antiga'),
        ]);

        $senha = UserService::resetarSenha($usuario);

        $this->assertNotSame('123', $senha);
        $this->assertFalse(Hash::check('123', $usuario->fresh()->password));
        $this->assertTrue($usuario->fresh()->must_change_password);
        $this->assertTrue(Hash::check($senha, $usuario->fresh()->password));
    }

    public function test_get_reset_route_no_longer_exists(): void
    {
        $usuario = $this->createUserWithRole(User::ROLE_ADMINISTRADOR);

        $this->actingAs($usuario)
            ->get('/dashboard/usuarios-senha-resetar/' . $usuario->id)
            ->assertStatus(404);
    }
}
