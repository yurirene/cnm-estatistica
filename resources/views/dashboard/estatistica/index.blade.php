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
</script>
@endpush
