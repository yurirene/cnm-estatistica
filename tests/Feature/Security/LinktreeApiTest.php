<?php

namespace Tests\Feature\Security;

use App\Models\LinkTree;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesUsersAndRoles;
use Tests\TestCase;

class LinktreeApiTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsersAndRoles;

    public function test_links_require_authentication(): void
    {
        $this->getJson('/api/linktree/links')->assertUnauthorized();
        $this->postJson('/api/linktree/links', [
            'name' => 'Teste',
            'url' => 'https://example.com',
        ])->assertUnauthorized();
    }

    public function test_login_returns_token_for_diretoria(): void
    {
        $user = $this->createUserWithRole(User::ROLE_DIRETORIA, [
            'password' => Hash::make('secret-pass'),
        ]);

        $response = $this->postJson('/api/linktree/validar-login', [
            'email' => $user->email,
            'password' => 'secret-pass',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['token']);
    }

    public function test_diretoria_can_crud_links_with_token(): void
    {
        $user = $this->createUserWithRole(User::ROLE_DIRETORIA, [
            'password' => Hash::make('secret-pass'),
        ]);
        $token = $user->createToken('linktree')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/linktree/links', [
                'name' => 'UMP',
                'url' => 'https://ump.org.br',
            ])
            ->assertOk();

        $this->assertDatabaseHas('link_trees', ['name' => 'UMP']);

        $link = LinkTree::query()->first();

        $this->withToken($token)
            ->getJson('/api/linktree/links')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->withToken($token)
            ->deleteJson('/api/linktree/remover-link/' . $link->id)
            ->assertOk();

        $this->assertDatabaseMissing('link_trees', ['id' => $link->id]);
    }

    public function test_non_diretoria_cannot_use_linktree_api(): void
    {
        $user = $this->createUserWithRole(User::ROLE_SINODAL, [
            'password' => Hash::make('secret-pass'),
            'sinodal_id' => $this->createSinodal()->id,
        ]);

        $this->postJson('/api/linktree/validar-login', [
            'email' => $user->email,
            'password' => 'secret-pass',
        ])->assertForbidden();

        $token = $user->createToken('linktree')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/linktree/links')
            ->assertForbidden();
    }
}
