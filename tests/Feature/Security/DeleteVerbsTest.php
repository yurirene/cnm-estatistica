<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesUsersAndRoles;
use Tests\TestCase;

class DeleteVerbsTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsersAndRoles;

    public function test_get_delete_routes_are_gone_for_instancias(): void
    {
        $bundle = $this->createLocalUser([
            'dashboard.sinodais.delete',
            'dashboard.federacoes.delete',
            'dashboard.locais.delete',
        ]);

        $this->actingAs($bundle['user'])
            ->withoutMiddleware([\App\Http\Middleware\AuthSistema::class])
            ->get('/dashboard/sinodais/' . $bundle['sinodal']->id . '/delete')
            ->assertStatus(405);

        $this->actingAs($bundle['user'])
            ->withoutMiddleware([\App\Http\Middleware\AuthSistema::class])
            ->get('/dashboard/federacoes/' . $bundle['federacao']->id . '/delete')
            ->assertStatus(405);

        $this->actingAs($bundle['user'])
            ->withoutMiddleware([\App\Http\Middleware\AuthSistema::class])
            ->get('/dashboard/umps-locais/' . $bundle['local']->id . '/delete')
            ->assertStatus(405);
    }

    public function test_guest_cannot_delete_via_delete_method(): void
    {
        $bundle = $this->createLocalUser();

        $this->delete('/dashboard/umps-locais/' . $bundle['local']->id . '/delete')
            ->assertRedirect();
    }
}
