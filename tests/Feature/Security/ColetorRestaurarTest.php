<?php

namespace Tests\Feature\Security;

use App\Models\ColetorDados;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesUsersAndRoles;
use Tests\TestCase;

class ColetorRestaurarTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsersAndRoles;

    public function test_guest_cannot_restaurar_via_public_get(): void
    {
        $localBundle = $this->createLocalUser();

        $this->get('/coletor-dados/restaurar/' . $localBundle['local']->id)
            ->assertStatus(404);

        $this->post(route('dashboard.coletor-dados.restaurar', $localBundle['local']->id))
            ->assertRedirect();
    }

    public function test_other_local_user_cannot_restaurar(): void
    {
        $owner = $this->createLocalUser(['dashboard.coletor-dados.restaurar']);
        $intruso = $this->createLocalUser(['dashboard.coletor-dados.restaurar']);

        ColetorDados::query()->create([
            'local_id' => $owner['local']->id,
            'ano' => (int) date('Y'),
            'status' => false,
            'resposta' => [],
        ]);

        $this->actingAs($intruso['user'])
            ->withoutMiddleware([\App\Http\Middleware\AuthSistema::class])
            ->post(route('dashboard.coletor-dados.restaurar', $owner['local']->id))
            ->assertForbidden();

        $this->assertDatabaseHas('coletor_dados', [
            'local_id' => $owner['local']->id,
        ]);
    }

    public function test_owner_can_restaurar_via_post(): void
    {
        $owner = $this->createLocalUser(['dashboard.coletor-dados.restaurar']);

        ColetorDados::query()->create([
            'local_id' => $owner['local']->id,
            'ano' => (int) date('Y'),
            'status' => false,
            'resposta' => [],
        ]);

        $this->actingAs($owner['user'])
            ->withoutMiddleware([\App\Http\Middleware\AuthSistema::class])
            ->post(route('dashboard.coletor-dados.restaurar', $owner['local']->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('coletor_dados', [
            'local_id' => $owner['local']->id,
            'ano' => (int) date('Y'),
        ]);
    }
}
