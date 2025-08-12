@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Formulário Complementar',
])
    
<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Campos Complementares</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-resposta">
                                Respostas
                            </button>
                        </div>
                    </div>
                    {!! Form::model(
                        $formulario,
                        [
                            'route' => ["dashboard.{$route}.update", $formulario->id],
                            'method' => 'PUT'
                        ]
                    ) !!}
                    <input type="checkbox"
                        class="parametro"
                        data-toggle="toggle"
                        data-onstyle="success"
                        data-on="Ativado"
                        data-id="status"
                        data-off="Desativado"
                        name="status"
                        id="status"
                        {{$formulario->status ? 'checked' : ''}}
                    >

                    <div id="fb-editor" class="mt-5"></div>
                    <div id="fb-rendered-form" class="mt-5" style="display: none;">
                        
                        {!! Form::hidden('formulario') !!}
                        <button class="btn btn-success" type="submit">Enviar</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        
    </div>
</div>

<div class="modal fade" id="modal-resposta" tabindex="-1" aria-labelledby="modal-createLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-createLabel">Respostas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <h4>Resposta de QTD UMPs de XX UMPs cadastradas</h4>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Gerar</button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('js')

<script>
    $(document).ready(function() {
        var formDataEdit = JSON.parse(@json($formulario->formulario));
        formBuilder = $(document.getElementById('fb-editor')).formBuilder(options)
            .promise
            .then(formBuilder => {
                formBuilder.actions.setData(formDataEdit);// after the builder loads, do you stuff here
            }
        );
        
    })
</script>
<script>
    var $fbEditor = $(document.getElementById('fb-editor'));
    var $formContainer = $(document.getElementById('fb-rendered-form'))
    var options = {
        i18n: {
            locale: 'pt-BR'
        },
        disabledAttrs: [
            "name",
            "access",
            "className",
            "placeholder",
            "step",
            'value',
            'subtype',
            'required'
        ],
        disableFields: [
            'autocomplete',
            'file',
            'hidden',
            'button',
            'date',
            'paragraph',
            'radio-group',
            'text',
            'textarea',
            'checkbox-group',
            'select'
        ],
        disabledActionButtons: ['data', 'edit' , 'clear'],
        onSave: function(evt, formData) {
            $fbEditor.toggle();
            $formContainer.toggle();
            $('input[name=formulario]').val(JSON.stringify(formData));
        },
    };
    $('.edit-form').click(function() {
        $fbEditor.toggle();
        $formContainer.toggle();
    });

</script>
@endpush