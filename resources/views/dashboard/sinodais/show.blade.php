@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Informações da ' .$sinodal->sigla,
    'subtitulo' => $sinodal->nome,
    'botaoRetorno' => route('dashboard.sinodais.index')
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-5 mb-5 mb-xl-0">
            <div class="card shadow p-3 h-100">
                <div class="card-header border-0">
                    <div class="row d-flex justify-content-between">
                        <div class="col-md-6">
                            <div class="d-grid gap-2">
                                <a class="btn btn-primary btn-sm  {{
                                        is_null($navegacaoSinodais['anterior'])
                                        ? 'disabled'
                                        : ''
                                }}"
                                    href="{{route('dashboard.sinodais.show', ($navegacaoSinodais['anterior'] ?? ''))}}"
                                >
                                    <i class="fas fa-arrow-left"></i> Anterior
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid gap-2">
                                <a class="btn btn-primary btn-sm {{
                                        is_null($navegacaoSinodais['proxima'])
                                        ? 'disabled'
                                        : ''
                                }}"
                                    href="{{route('dashboard.sinodais.show', ($navegacaoSinodais['proxima'] ?? ''))}}"
                                >
                                    <i class="fas fa-arrow-right"></i> Próxima
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="mb-3 text-center">Informações</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <h3>
                                <span class="badge badge-primary">Nome:</span>
                                {{ $sinodal->nome }}
                            </h3>
                            <h3>
                                <span class="badge badge-primary">Sínodo:</span>
                                {{ $sinodal->sinodo }}
                            </h3>
                            <h3>
                                <span class="badge badge-primary">Data de Organização:</span>
                                {{ $sinodal->data_organizacao_formatada }}
                            </h3>
                            <h3>
                                <span class="badge badge-primary">Redes Sociais:</span>
                                {{ $sinodal->midias_sociais }}
                            </h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-flex gap-2">
                            <button type="button" class="btn btn-primary mt-3"
                                data-toggle="modal"
                                data-target="#modal_diretoria"
                            >
                                <i class="fas fa-users"></i> Diretoria
                            </button>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                        <span>Federações Organizadas</span>
                                    </div>
                                    <div class="progress-percentage">
                                        <span>{{ $informacoes['total_federacoes_organizada'] }}%</span>
                                    </div>
                                </div>
                                <div class="progress" style="height: 15px">
                                    <div class="progress-bar bg-default" role="progressbar"
                                        aria-valuenow="{{ $informacoes['total_federacoes_organizada'] }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        style="width: {{ $informacoes['total_federacoes_organizada'] }}%;">
                                    </div>
                                    <span class="px-2">{{ $informacoes['total_federacoes_detalhe'] }}</span>
                                </div>
                            </div>

                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                        <span>UMPs Locais Organizadas</span>
                                    </div>
                                    <div class="progress-percentage">
                                        <span>{{ $informacoes['total_umps_organizada'] }}%</span>
                                    </div>
                                </div>
                                <div class="progress" style="height: 15px">
                                    <div class="progress-bar bg-default" role="progressbar"
                                        aria-valuenow="{{ $informacoes['total_umps_organizada'] }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        style="width: {{ $informacoes['total_umps_organizada'] }}%;">
                                    </div>
                                    <span class="px-2">{{ $informacoes['total_umps_detalhe'] }}</span>
                                </div>
                            </div>

                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                        <span>Não adotam Sociedades Internas</span>
                                    </div>
                                    <div class="progress-percentage">
                                        <span>{{ $informacoes['total_igrejas_n_sociedades'] }}%</span>
                                    </div>
                                </div>
                                <div class="progress" style="height: 15px">
                                    <div class="progress-bar bg-default" role="progressbar"
                                        aria-valuenow="{{ $informacoes['total_igrejas_n_sociedades'] }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        style="width: {{ $informacoes['total_igrejas_n_sociedades'] }}%;">
                                    </div>
                                    <span class="px-2">{{ $informacoes['total_n_si_detalhe'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-7 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Federações</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-auto" style="max-height: 600px">
                    <div class="row mt-3">
                        @if(count($federacoes))
                        @foreach($federacoes as $federacao)
                        <div class="col-md-6 col-xl-6 mt-3">
                            @include('dashboard.sinodais.partes.cards', $federacao)
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_informacoes_federacoes"
    data-backdrop="static"
    data-keyboard="false"
    aria-labelledby="modal_informacoes_federacoesLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modal_informacoes_federacoesLabel">
                Informações de <span id="sigla_federacao"></span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <b>Usuário:</b> <span id="usuario_federacao"></span>
            <a href="#"
                id="botao_modal_resetar_senha"
                class="btn btn-danger btn-sm text-center"
            >
                <i class="fas fa-key"></i> Resetar
            </a>
            <br>
            <button type="button" class="btn btn-sm btn-primary mt-3" id="botao_modal_diretoria">
                <i class="fas fa-users"></i> Diretoria
            </button>
            <div class="table-responsive mt-3">
                <table id="informacoes-federacao-table" class="table table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center">UMP</th>
                            <th class="text-center">Nº Sócios</th>
                            <th class="text-center">Relatório</th>
                            <th class="text-center">
                                Att Diretoria
                                <sup>
                                    <em
                                        class="fas fa-1x fa-info-circle"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Última atualização da diretoria"	
                                    ></em>
                                </sup>
                            </th>
                            <th class="text-center">Usuário</th>
                            <th class="text-center">Senha</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_diretoria"
    data-backdrop="static"
    data-keyboard="false"
    aria-labelledby="modal_diretoriaLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_diretoria_title">
                    Diretoria
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Informação atualizada em: {{ $diretoria['atualizacao'] }}</h5>
                <table class="table table-striped">
                    <tr>
                        <th>Cargo</th>
                        <th>Nome</th>
                        <th>Contato</th>
                    </tr>
                    @foreach($diretoria['cargos'] as $cargo => $dado)
                    <tr>
                        <td>{{ $cargo }}</td>
                        <td>{{ $dado['nome'] }}</td>
                        <td>{{ $dado['contato'] }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td>Secretários de Atividades</td>
                        <td colspan="2">{{ $diretoria['secretarios'] }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_diretoria_instancias"
    data-backdrop="static"
    data-keyboard="false"
    tabindex="-1"
    aria-labelledby="modal_diretoria_instanciasLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Diretoria
                </h5>
            </div>
            <div class="modal-body">
                <h5>Informação atualizada em: <span id="dir_atualizacao"></span></h5>
                <table class="table table-striped">
                    <thead>
                        <th>Cargo</th>
                        <th>Nome</th>
                        <th>Contato</th>
                    </thead>
                    <tbody id="dir_cargos">
                    </tbody>
                </table>
                <input type="text" id="dados_diretoria_modal">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModalDiretoriaInstancia()">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    const ROTA_RESET_SENHA = "{{ route('dashboard.usuarios.resetar-senha', ':id') }}";
    $(function() {
        var url = '{{ route("dashboard.datatables.informacao-federacoes", ":id") }}'
        $('#modal_informacoes_federacoes').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var nome = button.data('nome')
            var usuario = button.data('usuario')
            var usuarioId = button.data('usuarioid')
            var modal = $(this)

            let rota = url.replace(':id', id);

            modal.find('#sigla_federacao').text(nome);
            modal.find('#usuario_federacao').text(usuario);
            modal.find('#botao_modal_resetar_senha').attr('href', ROTA_RESET_SENHA.replace(':id', usuarioId))

            carregarDatatable(rota);

        });
    });
   function carregarDatatable(url) {
        $('#informacoes-federacao-table').DataTable().destroy();
        const datatable = $('#informacoes-federacao-table').DataTable({
            dom: 'rt',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                type: 'GET',
                dataSrc: function(json) {
                    if (json.diretoria_federacao) {
                        $('#botao_modal_diretoria').removeAttr('onclick');
                        $('#botao_modal_diretoria').attr('onClick', 'abrirModalDiretoriaInstancia(' + JSON.stringify(json.diretoria_federacao) + ');');
                        $('#botao_modal_diretoria').attr('disabled', false);
                    } else {
                        $('#botao_modal_diretoria').attr('disabled', true);
                    }

                    return json.data;
                }
            },
            columns: [
                {data: 'nome_ump'},
                {data: 'nro_socios'},
                {data: 'status_relatorio'},
                {
                    render: function (data, type, result) {
                        if (!result.diretoria.dados) {
                            return result.diretoria.atualizacao ?? 'Sem Informação'
                        }

                        const button = $(`<a href="#"
                            class="btn-link text-center"
                        >
                            ${result.diretoria.atualizacao}
                        </a>`)
                            .attr('onClick', 'abrirModalDiretoriaInstancia(' + JSON.stringify(result.diretoria.dados) + ');')
                            .get(0)
                            .outerHTML;

                        return button;
                    }
                },
                {data: 'usuario_email'},
                {
                    render: function (data, type, result) {
                        return `<a href="${ROTA_RESET_SENHA.replace(':id', result.usuario_id)}"
                            class="btn btn-danger btn-sm text-center"
                        >
                            <i class="fas fa-key"></i> Resetar
                        </a>`;
                    }
                },
            ]
        });
        datatable.draw();
   }
   
   $('#modal_diretoria_instancias').on('show.bs.modal', function (event) {
        var dados = JSON.parse($('#dados_diretoria_modal').val());

        $('#dir_cargos').empty();

        if (!dados || dados == 'undefined') {
            $('#dir_cargos').append(`
                <tr>
                    <td colspan="3" class="text-center">Sem Informação</td>
                </tr>
            `)

            return;
        }

        Object.entries(dados.cargos).forEach(([cargo, dado]) => {
            $('#dir_cargos').append(`
                <tr>
                    <td>${cargo}</td>
                    <td>${dado.nome ?? 'Sem Registro'}</td>
                    <td>${dado.contato ?? 'Sem Registro'}</td>
                </tr>
            `)
        })
        $('#dir_cargos').append(`
            <tr>
                <td>Secretários de Atividades</td>
                <td colspan="2">${dados.secretarios ?? 'Sem Secretários'}</td>
            </tr>
        `)

        $('#dir_atualizacao').text(dados.atualizacao)
    });

    function abrirModalDiretoriaInstancia(diretoria) {
        $('#dados_diretoria_modal').val(JSON.stringify(diretoria));
        $('#modal_diretoria_instancias').modal('show');
    }

    function fecharModalDiretoriaInstancia()
    {
        $('#modal_diretoria_instancias').modal('hide');
    }
</script>
@endpush
