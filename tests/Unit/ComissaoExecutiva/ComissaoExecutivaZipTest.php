<?php

namespace Tests\Unit\ComissaoExecutiva;

use App\Services\ComissaoExecutivaService;
use Tests\TestCase;
use ZipArchive;

class ComissaoExecutivaZipTest extends TestCase
{
    public function test_slug_para_arquivo_remove_acentos_e_usa_snake_case(): void
    {
        $this->assertSame('sao_paulo', ComissaoExecutivaService::slugParaArquivo('São Paulo'));
        $this->assertSame('ump_nacional', ComissaoExecutivaService::slugParaArquivo('UMP Nacional'));
        $this->assertSame('sem_nome', ComissaoExecutivaService::slugParaArquivo(null));
        $this->assertSame('sem_nome', ComissaoExecutivaService::slugParaArquivo('   '));
    }

    public function test_nome_unico_no_zip_evita_sobrescrever(): void
    {
        $usados = [];

        $primeiro = ComissaoExecutivaService::nomeUnicoNoZip('doc_abc.pdf', $usados);
        $segundo = ComissaoExecutivaService::nomeUnicoNoZip('doc_abc.pdf', $usados);
        $terceiro = ComissaoExecutivaService::nomeUnicoNoZip('doc_abc.pdf', $usados);

        $this->assertSame('doc_abc.pdf', $primeiro);
        $this->assertSame('doc_abc_1.pdf', $segundo);
        $this->assertSame('doc_abc_2.pdf', $terceiro);
    }

    public function test_montar_zip_adiciona_arquivos_e_evita_nomes_duplicados(): void
    {
        $dir = sys_get_temp_dir() . '/ce-zip-test-' . uniqid();
        mkdir($dir, 0755, true);

        $arquivoA = $dir . '/a.pdf';
        $arquivoB = $dir . '/b.pdf';
        $inexistente = $dir . '/nao-existe.pdf';
        file_put_contents($arquivoA, 'conteudo-a');
        file_put_contents($arquivoB, 'conteudo-b');

        $zipPath = $dir . '/saida.zip';
        $total = ComissaoExecutivaService::montarZip($zipPath, [
            ['path' => $arquivoA, 'nome' => 'doc_x.pdf'],
            ['path' => $arquivoB, 'nome' => 'doc_y.pdf'],
            ['path' => $inexistente, 'nome' => 'doc_z.pdf'],
        ]);

        $this->assertSame(2, $total);
        $this->assertFileExists($zipPath);

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipPath) === true);
        $this->assertNotFalse($zip->locateName('doc_x.pdf'));
        $this->assertNotFalse($zip->locateName('doc_y.pdf'));
        $this->assertFalse($zip->locateName('doc_z.pdf'));
        $this->assertSame('conteudo-a', $zip->getFromName('doc_x.pdf'));
        $zip->close();

        @unlink($arquivoA);
        @unlink($arquivoB);
        @unlink($zipPath);
        @rmdir($dir);
    }
}
