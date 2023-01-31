@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
'titulo' => 'Minhas Demandas'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Lista de Demandas</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5><i class="fas fa-filter"></i> Filtros</h5>
                                    <div class="row mb-2">
                                        <div class="col-md-4">
                                            <label>Origem</label>
                                            {!! Form::select('demanda_filtro', $demandas, null, ['class' => 'form-control', 'id' => 'demanda_filtro', 'placeholder' => '-']) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label>Nível</label>
                                            {!! Form::select('nivel_filtro', $niveis, null, ['class' => 'form-control', 'id' => 'nivel_filtro', 'placeholder' => '-']) !!}
                                        </div>
                                        <div class="col-md-4">
                                            <label>Status</label>
                                            {!! Form::select('status_filtro', $status, null, ['class' => 'form-control', 'id' => 'status_filtro', 'placeholder' => '-']) !!}
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-12">
                                            <button class="btn btn-primary" type="button" id="filtrar">Filtrar</button>
                                            <button class="btn btn-secondary" type="button" id="resetar">Limpar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        {!! $dataTable->table() !!}
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
    const table = $('#minhas-demandas-item-table');

    table.on('preXhr.dt', function(e, settings, data){
        data.nivel = $('#nivel_filtro').val();
        data.demanda = $('#demanda_filtro').val();
        data.status = $('#status_filtro').val();
    });

    $('#filtrar').on('click', function (){
        table.DataTable().ajax.reload();
        return false;
    });

    $('#resetar').on('click', function (){

        $('#nivel_filtro').val(null).trigger('change');
        $('#demanda_filtro').val(null).trigger('change');
        $('#status_filtro').val(null).trigger('change');
        table.DataTable().ajax.reload();
        return false;
    });



</script>

@endpush
