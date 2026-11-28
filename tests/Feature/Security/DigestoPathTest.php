<?php

namespace Tests\Feature\Security;

use App\Services\DigestoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DigestoPathTest extends TestCase
{
    use RefreshDatabase;

    public function test_path_traversal_is_rejected(): void
    {
        try {
            DigestoService::resolverCaminhoSeguro('../.env');
            $this->fail('Esperava 404 para path traversal.');
        } catch (HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function test_absolute_etc_passwd_is_rejected(): void
    {
        try {
            DigestoService::resolverCaminhoSeguro('/etc/passwd');
            $this->fail('Esperava 404 para path absoluto fora do diretório.');
        } catch (HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function test_safe_relative_path_inside_dir_is_resolved_when_file_exists(): void
    {
        $dir = base_path('storage/disgesto');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = $dir . '/teste-seguro.txt';
        file_put_contents($file, 'ok');

        try {
            $resolved = DigestoService::resolverCaminhoSeguro('teste-seguro.txt');
            $this->assertSame(realpath($file), realpath($resolved));
        } finally {
            @unlink($file);
        }
    }
}
