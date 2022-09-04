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
                                aria-selected="false">Relatórios
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="primeiro" role="tabpanel" aria-labelledby="primeiro-tab">
                            <div class="row mt-3">
                                <div class="col-md-12 mt-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <h2>Parâmetros</h2>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @foreach($parametros as $parametro)
                                                    @include('parametros.view',$parametro)
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="segundo" role="tabpanel" aria-labelledby="segundo-tab">
                            <div class="row mt-3">
                                <div class="col-md-12 mt-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="card shadow">
                                                        <div class="card-header">
                                                            Exportar Base de Dados Excel
                                                        </div>
                                                        <div class="card-body">
                                                            {!! Form::open(['method' => 'POST', 'route' => 'dashboard.estatistica.exportarExcel', 'class' => 'form-horizontal']) !!}
                                                            {!! Form::select('ano_referencia', $anos_referencias, null, ['class' => 'form-control']) !!}
                                                            <button class="btn btn-primary mt-2">
                                                                <i class="fas fa-file-excel"></i> Exportar
                                                            </button>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
