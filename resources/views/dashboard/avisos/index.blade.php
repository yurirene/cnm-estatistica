@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Avisos'
])


<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Avisos</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col">
                            <button class="btn btn-primary" type="button" data-target="#modal-create" data-toggle="modal">
                                <em class="fas fa-plus"></em> Aviso
                            </button>
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

<div class="modal fade" id="modal-create" tabindex="-1" aria-labelledby="modal-createLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-createLabel">Liberar Apps</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'route' =>
                ['dashboard.avisos.store']
            ]) !!}

            <div class="modal-body">

                <div class="form-group">
                    {!! Form::label('tipo', 'Tipo') !!}
                    {!! Form::select('tipo', $tipos, null, [
                        'class' => 'form-control',
                        'id' => 'tipo',
                        'required'=> false,
                        'autocomplete' => 'off',
                    ]) !!}
                </div>
                <div class="form-group">

                    {!! Form::label('titulo', 'Título') !!}
                    {!! Form::text('titulo', null, [
                        'class' => 'form-control',
                        'required'=> true,
                        'autocomplete' => 'off',
                    ]) !!}
                </div>
                <div class="form-group">

                    {!! Form::label('texto', 'Texto') !!}
                    {!! Form::textarea('texto', null, [
                        'class' => 'isSummernote',
                        'required'=> true,
                        'autocomplete' => 'off',
                    ]) !!}
                </div>
                <div class="form-group" style="display:none;" id="usuarios-div">
                    {!! Form::label('usuarios[]', 'Usuários') !!}
                    {!! Form::select('usuarios[]', [], null, [
                        'class' => 'form-control isSelect2',
                        'id' => 'usuarios',
                        'required'=> false,
                        'autocomplete' => 'off',
                        'multiple' => true,
                        'style' => 'width:100%;'
                    ]) !!}
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="S" name="modal" id="modal">
                    <label class="form-check-label" for="modal">
                        Modal
                    </label>
                </div>
            </div>
            <input type="hidden" id="sinodal_id" name="sinodal_id" />
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>


<div class="modal fade" id="modal-visualizados" tabindex="-1" aria-labelledby="modal-visualizadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-visualizadosLabel">Visualização do Aviso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table id="lidos-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center">Sinodal</th>
                                <th class="text-center">Visualizado</th>
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
@endsection

@push('js')

{!! $dataTable->scripts() !!}
<script>
    const CUSTOM = 4;
    const ROUTE = "{{ route('dashboard.avisos.listar-visualizados', ':id') }}";
    $(document).ready(function() {
        $('#usuarios').select2({
            ajax: {
                url: '{{ route("dashboard.avisos.get-usuarios") }}',
                processResults: function (data) {
                    return {
                        results: data.results
                    }
                }
            }
        });
    });

    $('#modal-visualizados').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var route = ROUTE.replace(':id', id);
        $('#lidos-table').DataTable().destroy();
        $('#lidos-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: route,
            columns: [
                {data: 'nome'},
                {
                    render: function (data, type, result) {
                        return `<div class="text-center"><span class="badge bg-${result.lido == 1 ? 'success' : 'danger'}">
                            ${result.lido == 1 ? 'Lido' : 'Pendente'}
                        </span></div>`;
                    }
                },
            ]
        });
    });

    $(".isSummernote").summernote({
        lang: 'pt-BR',
        height: 220,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']]
        ],
    });

    $('#tipo').on('change', function() {
        if ($(this).val() == CUSTOM) {
            $('#usuarios-div').show();
        }
    })
</script>
@endpush
