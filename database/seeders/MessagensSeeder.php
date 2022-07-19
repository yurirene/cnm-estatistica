<?php

namespace Database\Seeders;

use App\Models\BotMessage;
use Illuminate\Database\Seeder;

class MessagensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mensagens = [
            [
                'identificador' => 'boas_vindas',
                'mensagem' => 'Olá, {nome}! É bom ter você por aqui.',
                'keywords' => null,
                'resposta_de' => null
            ],

            [
                'identificador' => 'login',
                'mensagem' => 'Ainda não tenho registro do seu usuário. Para ter acesso as informações da plataforma, informe seu usuário.',
                'keywords' => null,
                'resposta_de' => null
            ],

            [
                'identificador' => 'login_erro',
                'mensagem' => 'Não encontrei seu usuário.',
                'keywords' => null,
                'resposta_de' => null
            ],

            [
                'identificador' => 'login_sucesso',
                'mensagem' => 'Aí sim! Encontrei aqui.',
                'keywords' => null,
                'resposta_de' => null
            ],

            [
                'identificador' => 'informacoes_usuario',
                'mensagem' => 'Usuário: {usuario}' . PHP_EOL
                            . 'UMP: {ump}' . PHP_EOL,
                
                'keywords' => null,
                'resposta_de' => null
            ],

            [
                'identificador' => 'lista_opcoes',
                'mensagem' => 'Escolha uma das opções:' . PHP_EOL
                            . '<b>[1]</b> - Totalizadores de Cadastros' . PHP_EOL
                            . '<b>[2]</b> - Quantidade de Relatórios Estatísticos de {instancia} Entregues' . PHP_EOL
                            . '<b>[3]</b> - Quantidade de Relatórios Estatísticos de {instancia} Faltantes' . PHP_EOL,
                
                'keywords' => null,
                'resposta_de' => null
            ],

            [
                'identificador' => 'quantidade_instancias_cadastradas',
                'mensagem' => '',
                'keywords' => '1',
                'resposta_de' => 'lista_opcoes',
            ],

            [
                'identificador' => 'quantidade_relatorios_entregues',
                'mensagem' => '{texto}',
                'keywords' => '2',
                'resposta_de' => 'lista_opcoes',
            ],

            [
                'identificador' => 'quantidade_relatorios_faltantes',
                'mensagem' => '{texto}',
                'keywords' => '3',
                'resposta_de' => 'lista_opcoes',
            ],

            [
                'identificador' => 'erro',
                'mensagem' => 'Desculpe, não encontrei nada sobre {mensagem}! Mais tarde peço para o criador me ensinar. Enquanto isso vamos recomeçar...',
                'keywords' => null,
                'resposta_de' => null
            ],
            
            
        ];
        foreach ($mensagens as $msg) {
            $resposta = null;
            if (!is_null($msg['resposta_de'])) {
                $resposta = BotMessage::where('identificador', $msg['resposta_de'])->first()->id;
            }
            BotMessage::updateOrCreate([
                'identificador' => $msg['identificador']
            ], [
                'identificador' => $msg['identificador'],
                'mensagem' => $msg['mensagem'],
                'keywords' => $msg['keywords'],
                'resposta_de' => $resposta
            ]);
        }
            
    }
}
