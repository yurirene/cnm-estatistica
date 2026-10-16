<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\GmailService;
use Google\Service\Gmail\Message;
use Google\Service\Gmail\MessagePart;
use Google\Service\Gmail\MessagePartBody;
use Google\Service\Gmail\MessagePartHeader;
use PHPUnit\Framework\TestCase;

class GmailServiceTest extends TestCase
{
    public function test_consulta_somente_com_alias(): void
    {
        $this->assertSame(
            'to:secretaria@cnm.org',
            GmailService::montarConsulta('secretaria@cnm.org', null)
        );
    }

    public function test_consulta_somente_com_marcador_com_espaco(): void
    {
        $this->assertSame(
            'label:"Nome do marcador"',
            GmailService::montarConsulta(null, 'Nome do marcador')
        );
    }

    public function test_consulta_com_alias_e_marcador(): void
    {
        $this->assertSame(
            'to:tesouraria@cnm.org label:Financeiro',
            GmailService::montarConsulta('tesouraria@cnm.org', 'Financeiro')
        );
    }

    public function test_consulta_vazia_sem_alias_e_sem_marcador(): void
    {
        $this->assertSame('', GmailService::montarConsulta('  ', ''));
    }

    public function test_perfil_permitido_grava_os_filtros(): void
    {
        $dados = GmailService::dadosParaGravar(
            User::ROLE_TESOURARIA,
            ' tesouraria@cnm.org ',
            ' Caixa '
        );

        $this->assertSame([
            'gmail_alias' => 'tesouraria@cnm.org',
            'gmail_label' => 'Caixa',
        ], $dados);
    }

    public function test_perfil_fora_da_lista_zera_os_filtros(): void
    {
        $dados = GmailService::dadosParaGravar(
            User::ROLE_LOCAL,
            'local@cnm.org',
            'Local'
        );

        $this->assertSame([
            'gmail_alias' => null,
            'gmail_label' => null,
        ], $dados);
    }

    public function test_mapeia_cabecalhos_e_trecho(): void
    {
        $mensagem = $this->mensagem([
            'Subject' => 'Ofício 12',
            'From' => 'Ana <ana@cnm.org>',
            'Date' => 'Thu, 16 Oct 2026 10:00:00 -0300',
        ], 'Resumo do ofício');

        $this->assertSame([
            'id' => 'abc',
            'threadId' => 'thr',
            'subject' => 'Ofício 12',
            'from' => 'Ana <ana@cnm.org>',
            'date' => 'Thu, 16 Oct 2026 10:00:00 -0300',
            'snippet' => 'Resumo do ofício',
        ], GmailService::mapearMensagem($mensagem));
    }

    public function test_cabecalho_ausente_vira_texto_vazio(): void
    {
        $mensagem = $this->mensagem([
            'Subject' => 'Sem remetente',
        ], '');

        $mapeada = GmailService::mapearMensagem($mensagem);

        $this->assertSame('Sem remetente', $mapeada['subject']);
        $this->assertSame('', $mapeada['from']);
        $this->assertSame('', $mapeada['date']);
    }

    public function test_prefere_html_e_decodifica_base64url(): void
    {
        $texto = $this->parteTexto('text/plain', 'só texto');
        $html = $this->parteTexto('text/html', '<p>Olá</p>');
        $mensagem = $this->mensagem(['Subject' => 'Com corpo'], '');
        $payload = $mensagem->getPayload();
        $payload->setMimeType('multipart/alternative');
        $payload->setParts([$texto, $html]);

        $detalhe = GmailService::montarDetalhe($mensagem);

        $this->assertSame('<p>Olá</p>', $detalhe['html']);
        $this->assertArrayNotHasKey('text', $detalhe);
    }

    public function test_coleta_anexo_com_nome_e_id(): void
    {
        $corpo = new MessagePartBody();
        $corpo->setAttachmentId('anexo_1');
        $corpo->setSize(1200);

        $anexo = new MessagePart();
        $anexo->setFilename('oficio.pdf');
        $anexo->setMimeType('application/pdf');
        $anexo->setBody($corpo);

        $mensagem = $this->mensagem(['Subject' => 'Com anexo'], '');
        $mensagem->getPayload()->setParts([$anexo]);

        $conteudo = GmailService::extrairConteudo($mensagem);

        $this->assertSame([[
            'index' => 0,
            'id' => 'anexo_1',
            'filename' => 'oficio.pdf',
            'mimeType' => 'application/pdf',
            'size' => 1200,
        ]], $conteudo['attachments']);
    }

    public function test_mensagem_fora_do_alias_e_recusada(): void
    {
        $mensagem = $this->mensagem([
            'To' => 'outra@cnm.org',
        ], '');

        $this->assertFalse(GmailService::compativelComFiltro(
            $mensagem,
            'secretaria@cnm.org',
            null
        ));
    }

    public function test_mensagem_fora_do_marcador_e_recusada(): void
    {
        $mensagem = $this->mensagem([
            'To' => 'Secretaria <secretaria@cnm.org>',
        ], '');
        $mensagem->setLabelIds(['INBOX']);

        $this->assertFalse(GmailService::compativelComFiltro(
            $mensagem,
            'secretaria@cnm.org',
            'Financeiro',
            ['Label_9']
        ));
    }

    public function test_mensagem_com_alias_e_marcador_e_aceita(): void
    {
        $mensagem = $this->mensagem([
            'To' => 'Secretaria <secretaria@cnm.org>',
        ], '');
        $mensagem->setLabelIds(['INBOX', 'Label_1']);

        $this->assertTrue(GmailService::compativelComFiltro(
            $mensagem,
            'secretaria@cnm.org',
            'Financeiro',
            ['Label_1']
        ));
    }

    private function parteTexto(string $mime, string $texto): MessagePart
    {
        $corpo = new MessagePartBody();
        $corpo->setData(rtrim(strtr(base64_encode($texto), '+/', '-_'), '='));

        $parte = new MessagePart();
        $parte->setMimeType($mime);
        $parte->setBody($corpo);

        return $parte;
    }

    private function mensagem(array $cabecalhos, string $snippet): Message
    {
        $headers = [];

        foreach ($cabecalhos as $nome => $valor) {
            $header = new MessagePartHeader();
            $header->setName($nome);
            $header->setValue($valor);
            $headers[] = $header;
        }

        $payload = new MessagePart();
        $payload->setHeaders($headers);

        $mensagem = new Message();
        $mensagem->setId('abc');
        $mensagem->setThreadId('thr');
        $mensagem->setSnippet($snippet);
        $mensagem->setPayload($payload);

        return $mensagem;
    }
}
