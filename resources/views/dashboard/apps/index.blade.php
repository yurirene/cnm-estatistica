@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Liberar Apps'
])


<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Liberar Apps</h3>
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

<div class="modal fade" id="liberar-app" tabindex="-1" aria-labelledby="liberar-appLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="liberar-appLabel">Liberar Apps</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'route' =>
                ['dashboard.apps.liberar']
            ]) !!}

            <div class="modal-body">

                <div class="form-group">
                    {!! Form::label('apps[]', 'Apps') !!}
                    {!! Form::select('apps[]', $apps, null, [
                        'class' => 'form-control isSelect2',
                        'id' => 'apps',
                        'required'=> false,
                        'autocomplete' => 'off',
                        'multiple' => true,
                        'style' => 'width:100%;'
                    ]) !!}
                </div>
            </div>
            <input type="hidden" id="sinodal_id" name="sinodal_id" />
            <input type="hidden" id="sinodais" name="sinodais" />
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="subtm" class="btn btn-primary">Liberar</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

@push('js')

{!! $dataTable->scripts() !!}
<script>
    $('#liberar-app').on('show.bs.modal', function (event) {

        $('#apps').val(null);
        $('#apps').trigger('change');
        var button = $(event.relatedTarget)
        var sinodal = button.data('sinodal')
        $('#sinodal_id').val(sinodal);
        var route = '{{ route("dashboard.apps.get-sinodal-apps", ":id") }}';
        route = route.replace(':id', sinodal);
        $.ajax({
            url: route,
            success: function(response) {
                $('#apps').val(response.apps);
                $('#apps').trigger('change');
            }
        })
    });

    function botoes()
    {
        return '<button class="btn btn-secondary" type="button" id="botao_editar"  onclick="editarEmBloco()">'
                        +'<i class="fas fa-pen"></i> Editar em Bloco'
                    +'</button>';
    }

    function checkboxAction()
    {
        var checkboxs = [];
        var botao = botoes();
        $("input:checkbox[name=linhas]:checked").each(function () {
            checkboxs.push($(this).val());
        });
        if (checkboxs.length > 0) {
            if (!$('.dt-buttons.btn-group.flex-wrap').find('#botao_editar').length){
                $('.dt-buttons.btn-group.flex-wrap').append(botao);
            }
        } else {
            $('#botao_editar').remove();
        }
    }

    $(document).on('click','#checkbox-master', function(){
        $('.isCheck').prop('checked', $(this).prop('checked'));
        checkboxAction();
    });

    $(document).on('click','#checkbox', function(){
        checkboxAction();
    });


    function editarEmBloco()
    {
        var ids = [];
        $("input:checkbox[name=linhas]:checked").each(function () {
            ids.push($(this).val());
        });
        console.log(ids);
        $('[name="sinodais"]').val(ids);

        $('#liberar-app').modal('show');
    }
</script>
@endpush
