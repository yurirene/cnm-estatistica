@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Tesouraria',
    'url_tutorial' => config('tutoriais.tesouraria.index'),
    'remover' => true
])
@include('dashboard.apps.tesouraria.cards',[
    'panorama' => $panorama
])


<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-pills" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active"
                                id="custom-tabs-four-home-tab"
                                data-toggle="pill"
                                href="#custom-tabs-four-home"
                                role="tab"
                                aria-controls="custom-tabs-four-home"
                                aria-selected="true">
                                Lançamento
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                id="custom-tabs-four-profile-tab"
                                data-toggle="pill"
                                href="#custom-tabs-four-profile"
                                role="tab"
                                aria-controls="custom-tabs-four-profile"
                                aria-selected="false">
                                Categoria
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                id="custom-tabs-four-relatorio-tab"
                                data-toggle="pill"
                                href="#custom-tabs-four-relatorio"
                                role="tab"
                                aria-controls="custom-tabs-four-relatorio"
                                aria-selected="false">
                                Relatório
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div
                            class="tab-pane fade show active"
                            id="custom-tabs-four-home"
                            role="tabpanel"
                            aria-labelledby="custom-tabs-four-home-tab"
                        >
                            <div class="row">
                                <div class="col-md-12">
                                    <h5><i class="fas fa-filter"></i> Filtros</h5>
                                    <div class="row mb-2" id="lancamentos-filtros">
                                        <div class="col-lg-3 col-md-6 mb-2">
                                            <label>Mês e ano</label>
                                            {!! Form::input(
                                                'month',
                                                'mes_filtro',
                                                null,
                                                ['class' => 'form-control', 'id' => 'mes_filtro']
                                            ) !!}
                                        </div>
                                        <div class="col-lg-2 col-md-6 mb-2">
                                            <label>Tipo</label>
                                            {!! Form::select(
                                                'tipo_filtro',
                                                ['' => 'Todos'] + $tipos,
                                                null,
                                                ['class' => 'form-control', 'id' => 'tipo_filtro']
                                            ) !!}
                                        </div>
                                        <div class="col-lg-2 col-md-6 mb-2">
                                            <label>Categoria</label>
                                            {!! Form::select(
                                                'categoria_filtro',
                                                ['' => 'Todas', 'sem' => 'Sem categoria'] + $categorias,
                                                null,
                                                ['class' => 'form-control', 'id' => 'categorias_filtro']
                                            ) !!}
                                        </div>
                                        <div class="col-lg-3 col-md-6 mb-2">
                                            <label>Período personalizado</label>
                                            {!! Form::text(
                                                'dt_lancamento_filtro',
                                                null,
                                                [
                                                    'class' => 'form-control isDateRange',
                                                    'id' => 'dt_lancamento_filtro',
                                                    'placeholder' => 'dd/mm/aaaa – dd/mm/aaaa',
                                                    'autocomplete' => 'off'
                                                ]
                                            ) !!}
                                        </div>
                                        <div class="col-lg-2 col-md-6 d-flex align-items-end mb-2">
                                            <button class="btn btn-primary mr-2" type="button" id="filtrar">Filtrar</button>
                                            <button class="btn btn-secondary" type="button" id="resetar">Limpar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">
                                {!! $dataTable->table(['class' => 'table w-100']) !!}
                            </div>
                        </div>
                        <div
                            class="tab-pane fade"
                            id="custom-tabs-four-profile"
                            role="tabpanel"
                            aria-labelledby="custom-tabs-four-profile-tab"
                        >
                            @include('dashboard.apps.tesouraria.categoria')
                        </div>
                        <div
                            class="tab-pane fade"
                            id="custom-tabs-four-relatorio"
                            role="tabpanel"
                            aria-labelledby="custom-tabs-four-relatorio-tab"
                        >
                            @include('dashboard.apps.tesouraria.relatorio')
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
@endsection

@push('css')
<style>
    .tesouraria-panorama {
        padding-bottom: 5rem;
    }

    .panorama-ano {
        appearance: none;
        -webkit-appearance: none;
        height: 31px;
        margin: 0;
        border: 1px solid #fff;
        border-radius: 0.25rem 0 0 0.25rem;
        background-color: #fff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%2332325d' stroke-width='1.5' d='M1 1.5l5 5 5-5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.55rem center;
        color: #32325d;
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.5;
        padding: 0 1.75rem 0 0.75rem;
        cursor: pointer;
    }

    .panorama-ano.inativo {
        background-color: transparent;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%23ffffff' stroke-width='1.5' d='M1 1.5l5 5 5-5'/%3E%3C/svg%3E");
        color: #fff;
        border-color: rgba(255, 255, 255, 0.6);
    }

    .panorama-ano option {
        color: #32325d;
        background-color: #fff;
    }

    .panorama-ano:focus {
        outline: none;
        box-shadow: 0 0 0 0.15rem rgba(255, 255, 255, 0.4);
    }
</style>
@endpush

@push('js')
{!! $dataTable->scripts() !!}

<script>
$('#table-categorias').DataTable({
    dom: 'frtip',
    responsive: true,
    processing: true,
});

</script>
<script>
    const table = $('#lancamento-table');
    const panoramaDados = $('#panorama-dados');
    const conjuntosPanorama = {
        mes: panoramaDados.data('mes')
    };
    const anosPanorama = panoramaDados.data('anos') || {};

    function aplicarPanorama(dados) {
        if (!dados) {
            return;
        }

        $('#val-saldo-inicial')
            .text(dados.saldoInicial)
            .removeClass('text-danger')
            .addClass(dados.saldoInicialClasse || '');
        $('#val-entradas').text(dados.entradas);
        $('#val-saidas').text(dados.saidas);
        $('#val-saldo-atual')
            .text(dados.saldoAtual)
            .removeClass('text-danger')
            .addClass(dados.saldoAtualClasse || '');
    }

    $('#panorama-ano').on('focus change', function () {
        $('#panorama-escopo button').removeClass('btn-white').addClass('btn-outline-white');
        $(this).removeClass('inativo');
        aplicarPanorama(anosPanorama[$(this).val()]);
    });

    $('#panorama-escopo button').on('click', function () {
        $('#panorama-escopo button').removeClass('btn-outline-white').addClass('btn-white');
        $('#panorama-ano').addClass('inativo');
        aplicarPanorama(conjuntosPanorama.mes);
    });

    table.on('preXhr.dt', function(e, settings, data){
        data.dt_lancamento = $('#dt_lancamento_filtro').val();
        data.mes = $('#mes_filtro').val();
        data.tipo = $('#tipo_filtro').val();
        data.categoria = $('#categorias_filtro').val();
    });

    $('#filtrar').on('click', function (){
        table.DataTable().ajax.reload();
        return false;
    });

    $('#resetar').on('click', function (){
        $('#mes_filtro').val('');
        $('#dt_lancamento_filtro').val('');
        $('#tipo_filtro').val('');
        $('#categorias_filtro').val('');
        table.DataTable().ajax.reload();
        return false;
    });

    $('#mes_filtro').on('change', function () {
        if ($(this).val()) {
            $('#dt_lancamento_filtro').val('');
        }
    });

    $('#dt_lancamento_filtro').on('apply.daterangepicker', function () {
        $('#mes_filtro').val('');
    });

    $('#filtrar-mes-corrente').on('click', function (event) {
        event.preventDefault();
        $('#custom-tabs-four-home-tab').tab('show');
        $('#dt_lancamento_filtro').val('');
        $('#mes_filtro').val($(this).data('mes'));
        table.DataTable().ajax.reload();

        const destino = $('#lancamentos-filtros');
        if (destino.length) {
            $('html, body').animate({ scrollTop: destino.offset().top - 80 }, 300);
        }
    });
</script>
@endpush
