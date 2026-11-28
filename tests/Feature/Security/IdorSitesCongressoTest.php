<?php

namespace Tests\Feature\Security;

use App\Models\CongressoNacional\DelegadoCongressoNacional;
use App\Models\User;
use App\Services\Apps\SiteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Support\CreatesUsersAndRoles;
use Tests\TestCase;

class IdorSitesCongressoTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsersAndRoles;

    public function test_sinodal_cannot_update_another_sinodal_site_config(): void
    {
        $a = $this->createSinodalUser();
        $b = $this->createSinodal();

        $this->actingAs($a['user']);

        try {
            SiteService::atualizarConfig($b->id, [
                'chave' => 0,
                'config' => 'titulo',
                'valor' => 'hack',
            ]);
            $this->fail('Esperava 403 para IDOR de site.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_sinodal_cannot_delete_another_sinodal_delegado(): void
    {
        $a = $this->createSinodalUser(['dashboard.cn.sinodal.delegado.delete']);
        $b = $this->createSinodal();

        $delegado = DelegadoCongressoNacional::query()->create([
            'nome' => 'Delegado B',
            'sinodal_id' => $b->id,
            'status' => DelegadoCongressoNacional::STATUS_PENDENTE,
            'oficial' => DelegadoCongressoNacional::OFICIAL_NAO,
        ]);

        $this->actingAs($a['user'])
            ->withoutMiddleware([\App\Http\Middleware\AuthSistema::class])
            ->delete(route('dashboard.cn.sinodal.delegado.delete', $delegado->id))
            ->assertForbidden();

        $this->assertDatabaseHas('congresso_nacional_delegados', [
            'id' => $delegado->id,
        ]);
    }

    public function test_owner_sinodal_can_delete_own_delegado(): void
    {
        $a = $this->createSinodalUser(['dashboard.cn.sinodal.delegado.delete']);

        $delegado = DelegadoCongressoNacional::query()->create([
            'nome' => 'Delegado A',
            'sinodal_id' => $a['sinodal']->id,
            'status' => DelegadoCongressoNacional::STATUS_PENDENTE,
            'oficial' => DelegadoCongressoNacional::OFICIAL_NAO,
        ]);

        $this->actingAs($a['user'])
            ->withoutMiddleware([\App\Http\Middleware\AuthSistema::class])
            ->delete(route('dashboard.cn.sinodal.delegado.delete', $delegado->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('congresso_nacional_delegados', [
            'id' => $delegado->id,
        ]);
    }
}
