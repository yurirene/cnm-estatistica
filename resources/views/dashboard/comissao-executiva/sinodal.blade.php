<div class="col-xl-12 mb-5 mb-xl-0">
    <div class="card shadow p-3">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">
                        Envio de Documentos para CE {{ $reuniao['local'] ?? '' }} - {{ $reuniao['ano'] ?? '' }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                @if(!empty($reuniao) && $reuniao['status'] == 1)
                    <h5>Formulário</h5>
                    {!! Form::open([
                        'method' => 'POST',
                        'route' => 'dashboard.ce-sinodal.enviar-documento',
                        'files' => true
                    ]) !!}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('tipo', 'Tipo') !!}
                                {!! Form::select(
                                    'tipo',
                                    $tipos,
                                    null,
                                    [
                                        'class' => 'form-control',
                                        'required' => true,
                                        'autocomplete' => 'off',
                                        'id' => 'tipo'
                                    ]
                                ) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('titulo', 'Título', ['id' => 'titulo_titulo']) !!}
                                {!! Form::text(
                                    'titulo',
                                    null,
                                    [
                                        'class' => 'form-control',
                                        'required' => true,
                                        'autocomplete' => 'off',
                                    ]
                                ) !!}
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                {!! Form::label('arquivo', 'Documento', ['id' => 'titulo_doc']) !!}
                                {!! Form::file(
                                    'arquivo',
                                    ['required' => 'required', 'class' => 'form-control', ]
                                ) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <button class="btn btn-success">Enviar</button>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
                @endif
            </div>
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table w-100']) !!}
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    const TIPO_DOCUMENTO_SINODAL = 3;

    $('#tipo').on('change', function () {
        if ($(this).val() != TIPO_DOCUMENTO_SINODAL) {
            $('#titulo_titulo').text('Nome do Delegado');
            $('#titulo_doc').text('Credencial');
        } else {
            $('#titulo_titulo').text('Título');
            $('#titulo_doc').text('Documento');
        }
    });
</script>
@endpush
