@extends('layouts.app')

@section('content')

@include('dashboard.apps.tesouraria.cards',[
    'totalizadores' => $totalizadores
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
                                    <div class="row mb-2">
                                        <div class="col-lg-3 col-md-6">
                                            <label>Data Lançamento</label>
                                            {!! Form::text(
                                                'dt_lancamento_filtro',
                                                null,
                                                ['class' => 'form-control isDateRange','id' => 'dt_lancamento_filtro']
                                            ) !!}
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <label>Tipo</label>
                                            {!! Form::select(
                                                'tipo_filtro',
                                                ["" => "Selecione um tipo"] + $tipos,
                                                null,
                                                ['class' => 'form-control', 'id' => 'tipo_filtro']
                                            ) !!}
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <label>Categoria</label>
                                            {!! Form::select(
                                                'categoria_filtro',
                                                ["" => "Selecione uma categoria"] + $categorias,
                                                null,
                                                ['class' => 'form-control', 'id' => 'categorias_filtro']
                                            ) !!}
                                        </div>
                                        <div class="col-lg-3 col-md-6 d-flex align-items-end">
                                            <button class="btn btn-primary" type="button" id="filtrar">Filtrar</button>
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

    table.on('preXhr.dt', function(e, settings, data){
        data.dt_lancamento = $('#dt_lancamento_filtro').val();
        data.tipo = $('#tipo_filtro').val();
        data.categoria = $('#categorias_filtro').val();
    });

    $('#filtrar').on('click', function (){
        table.DataTable().ajax.reload();
        return false;
    });

    $('#resetar').on('click', function (){

        $('#nivel_filtro').val(null).trigger('change');
        $('#usuario_filtro').val(null).trigger('change');
        $('#status_filtro').val(null).trigger('change');
        table.DataTable().ajax.reload();
        return false;
    });



</script>
@endpush
