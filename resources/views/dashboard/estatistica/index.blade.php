@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Estatística'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Painel de Estatística</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist" style="line-height: 40px;">

                        <li class="nav-item" role="presentation">
                            <button class="nav-link active"
                                id="terceiro-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#terceiro"
                                type="button"
                                role="tab"
                                aria-controls="terceiro"
                                aria-selected="false">Relatórios
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="primeiro-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#primeiro"
                                type="button"
                                role="tab"
                                aria-controls="primeiro"
                                aria-selected="true">Configurações
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="segundo-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#segundo"
                                type="button"
                                role="tab"
                                aria-controls="segundo"
                                aria-selected="false">Base de Dados
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        @include('dashboard.estatistica.tabs.formularios')
                        @include('dashboard.estatistica.tabs.parametros')
                        @include('dashboard.estatistica.tabs.base')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="token" value="{{ csrf_token() }}" />
@endsection

@push('js')
<script>
    $('.parametro').on('change', function() {
        let valor = $(this).prop('checked');
        let token = $('#token').val();
        let id = $(this).data('id');
        let route = "{{ route('dashboard.estatistica.atualizarParametro')}}";
        $.ajax({
            url: route,
            type: 'POST',
            data: {
                _token: token,
                id: id,
                valor: valor
            }
        }).done((response) => {
            iziToast.show({
                title: 'Sucesso!',
                message: response.mensagem,
                position: 'topRight',
            });
        }).catch((error) => {
            iziToast.error({
                title: 'Erro!',
                message: response.mensagem,
                position: 'topRight',
            });
        });
    });
    $('.btn-parametro').on('click', function() {
        let input = $(this).parents('.input-group').find('input');
        let valor = input.val();
        let token = $('#token').val();
        let id = input.data('id');
        let route = "{{ route('dashboard.estatistica.atualizarParametro')}}";
        $.ajax({
            url: route,
            type: 'POST',
            data: {
                _token: token,
                id: id,
                valor: valor
            }
        }).done((response) => {
            iziToast.show({
                title: 'Sucesso!',
                message: response.mensagem,
                position: 'topRight',
            });
        }).catch((error) => {
            iziToast.error({
                title: 'Erro!',
                message: response.mensagem,
                position: 'topRight',
            });
        });
    })
    function inicializarTabelaValorAciAno() {
        let tabela = $('#valores-aci-ano-table');
        if (!tabela.length || $.fn.DataTable.isDataTable(tabela)) {
            if ($.fn.DataTable.isDataTable(tabela)) {
                tabela.DataTable().columns.adjust();
            }
            return;
        }
        tabela.DataTable({
            order: [[0, 'desc']],
            paging: false,
            info: false,
            columnDefs: [
                { type: 'num', targets: 0 },
                { orderable: false, targets: [1, 2] }
            ]
        });
    }
    function salvarValorAciAno(ano, valor) {
        let token = $('#token').val();
        let route = "{{ route('dashboard.estatistica.atualizarValorAciAno')}}";
        $.ajax({
            url: route,
            type: 'POST',
            data: {
                _token: token,
                ano: ano,
                valor: valor
            }
        }).done((response) => {
            iziToast.show({
                title: 'Sucesso!',
                message: response.mensagem,
                position: 'topRight',
            });
            window.location.reload();
        }).catch((error) => {
            iziToast.error({
                title: 'Erro!',
                message: (error.responseJSON && error.responseJSON.mensagem) ? error.responseJSON.mensagem : 'Erro ao atualizar valor da ACI',
                position: 'topRight',
            });
        });
    }
    $('#valores-aci-ano-table').on('click', '.btn-valor-aci-ano', function() {
        let linha = $(this).closest('tr');
        salvarValorAciAno($(this).data('ano'), linha.find('.valor-aci-ano').val());
    });
    $('.btn-novo-valor-aci-ano').on('click', function() {
        salvarValorAciAno($('#novo-ano-aci').val(), $('#novo-valor-aci').val());
    });
    $('button[data-bs-target="#primeiro"]').on('shown.bs.tab', function () {
        inicializarTabelaValorAciAno();
    });
    inicializarTabelaValorAciAno();
</script>
@endpush
