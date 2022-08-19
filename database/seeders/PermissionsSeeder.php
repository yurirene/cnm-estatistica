<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Yajra\Acl\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'name' => 'Listar Usuário',
                'slug' => 'dashboard.usuarios.index',
                'resource' => 'usuario',
            ],
            [
                'name' => 'Salvar Usuário',
                'slug' => 'dashboard.usuarios.store',
                'resource' => 'usuario',
            ],
            [
                'name' => 'Criar Usuário',
                'slug' => 'dashboard.usuarios.create',
                'resource' => 'usuario',
            ],
            [
                'name' => 'Editar Usuário',
                'slug' => 'dashboard.usuarios.edit',
                'resource' => 'usuario',
            ],
            [
                'name' => 'Atualizar Usuário',
                'slug' => 'dashboard.usuarios.update',
                'resource' => 'usuario',
            ],
            [
                'name' => 'Deletar Usuário',
                'slug' => 'dashboard.usuarios.delete',
                'resource' => 'usuario',
            ],

            [
                'name' => 'Resetar Senha Usuário',
                'slug' => 'dashboard.usuarios.reset-senha',
                'resource' => 'usuario',
            ],
           
            [
                'name' => 'Listar Sinodais',
                'slug' => 'dashboard.sinodais.index',
                'resource' => 'sinodais',
            ],
           
            [
                'name' => 'Visualizar Sinodal',
                'slug' => 'dashboard.sinodais.show',
                'resource' => 'sinodais',
            ],
            [
                'name' => 'Salvar Sinodais',
                'slug' => 'dashboard.sinodais.store',
                'resource' => 'sinodais',
            ],
            [
                'name' => 'Criar Sinodais',
                'slug' => 'dashboard.sinodais.create',
                'resource' => 'sinodais',
            ],
            [
                'name' => 'Editar Sinodais',
                'slug' => 'dashboard.sinodais.edit',
                'resource' => 'sinodais',
            ],
            [
                'name' => 'Atualizar Sinodais',
                'slug' => 'dashboard.sinodais.update',
                'resource' => 'sinodais',
            ],
            [
                'name' => 'Deletar Sinodais',
                'slug' => 'dashboard.sinodais.delete',
                'resource' => 'sinodais',
            ],
            

            [
                'name' => 'Listar Federações',
                'slug' => 'dashboard.federacoes.index',
                'resource' => 'federacoes',
            ],
           
            [
                'name' => 'Visualizar Federação',
                'slug' => 'dashboard.federacoes.show',
                'resource' => 'federacoes',
            ],
            [
                'name' => 'Salvar Federações',
                'slug' => 'dashboard.federacoes.store',
                'resource' => 'federacoes',
            ],
            [
                'name' => 'Criar Federações',
                'slug' => 'dashboard.federacoes.create',
                'resource' => 'federacoes',
            ],
            [
                'name' => 'Editar Federações',
                'slug' => 'dashboard.federacoes.edit',
                'resource' => 'federacoes',
            ],
            [
                'name' => 'Atualizar Federações',
                'slug' => 'dashboard.federacoes.update',
                'resource' => 'federacoes',
            ],
            [
                'name' => 'Deletar Federações',
                'slug' => 'dashboard.federacoes.delete',
                'resource' => 'federacoes',
            ],


            [
                'name' => 'Listar UMPs Locais',
                'slug' => 'dashboard.locais.index',
                'resource' => 'umps_locais',
            ],

            [
                'name' => 'Visualizar UMP Local',
                'slug' => 'dashboard.locais.show',
                'resource' => 'umps_locais',
            ],

            [
                'name' => 'Salvar UMPs Locais',
                'slug' => 'dashboard.locais.store',
                'resource' => 'umps_locais',
            ],
            [
                'name' => 'Criar UMPs Locais',
                'slug' => 'dashboard.locais.create',
                'resource' => 'umps_locais',
            ],
            [
                'name' => 'Editar UMPs Locais',
                'slug' => 'dashboard.locais.edit',
                'resource' => 'umps_locais',
            ],
            [
                'name' => 'Atualizar UMPs Locais',
                'slug' => 'dashboard.locais.update',
                'resource' => 'umps_locais',
            ],
            [
                'name' => 'Deletar UMPs Locais',
                'slug' => 'dashboard.locais.delete',
                'resource' => 'umps_locais',
            ],

            [
                'name' => 'Atualizar Informações das UMPs Locais',
                'slug' => 'dashboard.locais.update-info',
                'resource' => 'umps_locais',
            ],
           
           
            [
                'name' => 'Listar Atividades',
                'slug' => 'dashboard.atividades.index',
                'resource' => 'atividades',
            ],

            [
                'name' => 'Calendário de Atividades',
                'slug' => 'dashboard.atividades.calendario',
                'resource' => 'atividades',
            ],

            [
                'name' => 'Confirmar Participação na Atividades',
                'slug' => 'dashboard.atividades.confirmar',
                'resource' => 'atividades',
            ],

            [
                'name' => 'Salvar Atividades',
                'slug' => 'dashboard.atividades.store',
                'resource' => 'atividades',
            ],
            [
                'name' => 'Criar Atividades',
                'slug' => 'dashboard.atividades.create',
                'resource' => 'atividades',
            ],
            [
                'name' => 'Editar Atividades',
                'slug' => 'dashboard.atividades.edit',
                'resource' => 'atividades',
            ],
            [
                'name' => 'Atualizar Atividades',
                'slug' => 'dashboard.atividades.update',
                'resource' => 'atividades',
            ],
            [
                'name' => 'Deletar Atividades',
                'slug' => 'dashboard.atividades.delete',
                'resource' => 'atividades',
            ],


            [
                'name' => 'Visualizar Formulário UMP Local',
                'slug' => 'dashboard.formularios-locais.index',
                'resource' => 'formularios_ump',
            ],
            [
                'name' => 'Salvar Formulário UMP Local',
                'slug' => 'dashboard.formularios-locais.store',
                'resource' => 'formularios_ump',
            ],
            [
                'name' => 'Ver Formulário UMP Local',
                'slug' => 'dashboard.formularios-locais.view',
                'resource' => 'formularios_ump',
            ],


            [
                'name' => 'Visualizar Formulário Federação',
                'slug' => 'dashboard.formularios-federacoes.index',
                'resource' => 'formularios_fed',
            ],           

            [
                'name' => 'Salvar Formulário Federação',
                'slug' => 'dashboard.formularios-federacoes.store',
                'resource' => 'formularios_fed',
            ],
            [
                'name' => 'Ver Formulário Federação',
                'slug' => 'dashboard.formularios-federacoes.view',
                'resource' => 'formularios_fed',
            ],
            [
                'name' => 'Resumo Totalizador Formulário Federação',
                'slug' => 'dashboard.formularios-federacoes.resumo',
                'resource' => 'formularios_fed',
            ],


            [
                'name' => 'Visualizar Formulário Sinodal',
                'slug' => 'dashboard.formularios-sinodais.index',
                'resource' => 'formularios_sin',
            ],           

            [
                'name' => 'Salvar Formulário Sinodal',
                'slug' => 'dashboard.formularios-sinodais.store',
                'resource' => 'formularios_sin',
            ],
            [
                'name' => 'Ver Formulário Sinodal',
                'slug' => 'dashboard.formularios-sinodais.view',
                'resource' => 'formularios_sin',
            ],
            [
                'name' => 'Resumo Totalizador Formulário Sinodal',
                'slug' => 'dashboard.formularios-sinodais.resumo',
                'resource' => 'formularios_sin',
            ],
            [
                'name' => 'Importar Planilha Formulário Sinodal',
                'slug' => 'dashboard.formularios-sinodais.importar',
                'resource' => 'formularios_sin',
            ],
            [
                'name' => 'Validar Planilha Formulário Sinodal',
                'slug' => 'dashboard.formularios-sinodais.importar-validar',
                'resource' => 'formularios_sin',
            ],
            [
                'name' => 'Listar Federações da Sinodal',
                'slug' => 'dashboard.formularios-sinodais.get-federacoes',
                'resource' => 'formularios_sin',
            ],
           

            /** Sinodal - informação */
            [
                'name' => 'Atualizar Informações Sinodais',
                'slug' => 'dashboard.sinodais.update-info',
                'resource' => 'sinodais',
            ],
            [
                'name' => 'Atualizar Informações Federação',
                'slug' => 'dashboard.federacoes.update-info',
                'resource' => 'federacoes',
            ],
            
            /**
             * Controle de ACI da Tesouraria
             */

            [
                'name' => 'Listar ACI',
                'slug' => 'dashboard.comprovante-aci.index',
                'resource' => 'comprovante-aci',
            ],

            [
                'name' => 'Status da ACI',
                'slug' => 'dashboard.comprovante-aci.status',
                'resource' => 'comprovante-aci',
            ],

            [
                'name' => 'Visualizar ACI',
                'slug' => 'dashboard.comprovante-aci.store',
                'resource' => 'comprovante-aci',
            ],

            /**
             * Secretaria de Eventos
             */

            [
                'name' => 'Listar Eventos',
                'slug' => 'dashboard.eventos.index',
                'resource' => 'eventos',
            ],

            [
                'name' => 'Visualizar Evento',
                'slug' => 'dashboard.eventos.show',
                'resource' => 'eventos',
            ],

            [
                'name' => 'Salvar Eventos',
                'slug' => 'dashboard.eventos.store',
                'resource' => 'eventos',
            ],
            [
                'name' => 'Criar Eventos',
                'slug' => 'dashboard.eventos.create',
                'resource' => 'eventos',
            ],
            [
                'name' => 'Editar Eventos',
                'slug' => 'dashboard.eventos.edit',
                'resource' => 'eventos',
            ],
            [
                'name' => 'Atualizar Eventos',
                'slug' => 'dashboard.eventos.update',
                'resource' => 'eventos',
            ],
            [
                'name' => 'Deletar Eventos',
                'slug' => 'dashboard.eventos.delete',
                'resource' => 'eventos',
            ],



            /**
             * Formulário Secretarias
             */

            [
                'name' => 'Listar Formularios',
                'slug' => 'dashboard.pesquisas.index',
                'resource' => 'pesquisas',
            ],

            [
                'name' => 'Visualizar Formulario',
                'slug' => 'dashboard.pesquisas.show',
                'resource' => 'pesquisas',
            ],

            [
                'name' => 'Salvar Formulario',
                'slug' => 'dashboard.pesquisas.store',
                'resource' => 'pesquisas',
            ],
            [
                'name' => 'Criar Formulario',
                'slug' => 'dashboard.pesquisas.create',
                'resource' => 'pesquisas',
            ],
            [
                'name' => 'Editar Formulario',
                'slug' => 'dashboard.pesquisas.edit',
                'resource' => 'pesquisas',
            ],
            [
                'name' => 'Atualizar Formulario',
                'slug' => 'dashboard.pesquisas.update',
                'resource' => 'pesquisas',
            ],
            [
                'name' => 'Deletar Formulario',
                'slug' => 'dashboard.pesquisas.delete',
                'resource' => 'pesquisas',
            ],
            [
                'name' => 'Responder Formulario',
                'slug' => 'dashboard.pesquisas.responder',
                'resource' => 'pesquisas',
            ],

            [
                'name' => 'Alterar Status do Formulario',
                'slug' => 'dashboard.pesquisas.status',
                'resource' => 'pesquisas',
            ],
            
            [
                'name' => 'Configurar Pesquisa',
                'slug' => 'dashboard.pesquisas.configuracoes',
                'resource' => 'pesquisas',
            ],

            [
                'name' => 'Atualizar Configurações da Pesquisa',
                'slug' => 'dashboard.pesquisas.configuracoes-update',
                'resource' => 'pesquisas',
            ],

            [
                'name' => 'Relatório da Pesquisa',
                'slug' => 'dashboard.pesquisas.relatorio',
                'resource' => 'pesquisas',
            ],

            [
                'name' => 'Limpar Respostas da Pesquisa',
                'slug' => 'dashboard.pesquisas.limpar-respostas',
                'resource' => 'pesquisas',
            ],

            [
                'name' => 'Gerar Relatório Excel da Pesquisa',
                'slug' => 'dashboard.pesquisas.relatorio.excel',
                'resource' => 'pesquisas',
            ],

            [
                'name' => 'Acompanhamento de Preenchimento',
                'slug' => 'dashboard.pesquisas.acompanhar',
                'resource' => 'pesquisas',
            ],

            [
                'name' => 'Datatable Log Erros',
                'slug' => 'dashboard.datatables.log-erros',
                'resource' => 'datatables-ajax',
            ],

            [
                'name' => 'Datatable Informação Federações (UMPs)',
                'slug' => 'dashboard.datatables.informacao-federacoes',
                'resource' => 'datatables-ajax',
            ],

            [
                'name' => 'Datatable Acompanhar Pesquisa Sinodal',
                'slug' => 'dashboard.datatables.pesquisas.sinodais',
                'resource' => 'datatables-ajax',
            ],

            [
                'name' => 'Datatable Acompanhar Pesquisa Federação',
                'slug' => 'dashboard.datatables.pesquisas.federacoes',
                'resource' => 'datatables-ajax',
            ],
            
            [
                'name' => 'Datatable Acompanhar Pesquisa UMP Local',
                'slug' => 'dashboard.datatables.pesquisas.locais',
                'resource' => 'datatables-ajax',
            ],

            // SECRETARIA DE ESTATISTICA

            
            
            [
                'name' => 'Configurações Estatística',
                'slug' => 'dashboard.estatistica.index',
                'resource' => 'estatistica',
            ],
            [
                'name' => 'Salvar Parametros Estatística',
                'slug' => 'dashboard.estatistica.atualizarParametro',
                'resource' => 'estatistica',
            ],

        ];

        try {
            foreach ($permissions as $role) {
                Permission::create($role);
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
