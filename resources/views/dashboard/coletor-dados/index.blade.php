@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Coletor Dados'
])


<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Formulários</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        {!! $dataTable->table() !!}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modal-create" tabindex="-1" aria-labelledby="modal-createLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-createLabel">Gerar Relatório</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'route' =>
                ['dashboard.coletor-dados.store']
            ]) !!}

            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('quantidade', 'Quantidade') !!}
                    {!! Form::text('quantidade', null, [
                        'class' => 'form-control',
                        'required'=> true,
                        'autocomplete' => 'off',
                    ]) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Gerar</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection

@push('js')

{!! $dataTable->scripts() !!}
<script>
    const ROUTE_DELETE = "{{ route('dashboard.coletor-dados.delete', ':id') }}";


    function apagar(id) {
        let rota = ROUTE_DELETE.replace(':id', id);
        deleteRegistro(rota);
    }

    $('#modal-resposta').on('show.bs.modal', function () {
        const button = $(event.relatedTarget)
        let resposta = button.data('resposta');

        console.log(resposta);
        
    })
</script>
@endpush