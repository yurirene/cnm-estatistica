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
                'mensagem' => 'Olá, {nome}! É bom ter você por aqui. \nPara ter acesso as informações da plataforma, informe seu usuário.',
                'keywords' => null,
                'resposta_de' => null
            ]
        ];
        foreach ($mensagens as $msg) {
            BotMessage::updateOrCreate([
                'identificador' => $msg['identificador']
            ], [
                'identificador' => $msg['identificador'],
                'mensagem' => $msg['mensagem'],
                'keywords' => $msg['keywords'],
                'resposta_de' => $msg['resposta_de']
            ]);
        }
            
    }
}
