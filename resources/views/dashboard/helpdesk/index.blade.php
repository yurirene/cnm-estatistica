@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
'titulo' => 'Problemas e Sugestões'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-4 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Problemas</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>
                        Encontrou alguma dificuldade, problema ou mal funcionamento?
                    </p>

                    <a
                    href="https://api.whatsapp.com/send?phone=5592986115853&text=Estou%20com%20problemas%20para%20:"
                    class="btn btn-success"
                    >
                        <i class="fab fa-whatsapp"></i>
                        Fale comigo
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-8 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-8">
                            <h3 class=" mb-0">Sugestões</h3>
                        </div>
                        <div class="col-4 text-right">
                            <button
                                type="button"
                                class="btn btn-sm btn-default"
                                data-toggle="modal"
                                data-target="#modal-sugestao"
                            >
                                <em
                                    class="fas fa-list"
                                ></em> Minhas Sugestões
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>
                        Tem alguma sugestão sobre algo que possa ser aprimorado ou algo que possa ser adicionado
                        na plataforma para auxilio das instâncias da UMP?
                    </p>
                    {!! Form::open(
                        [
                            'method' => 'POST',
                            'route' => ['dashboard.helpdesk.store']
                    ]) !!}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('titulo', 'Título') !!}<small class="text-danger">*</small>
                                {!! Form::text('titulo', null, [
                                    'class' => 'form-control',
                                    'required'=> true,
                                    'autocomplete' => 'off',
                                    'placeholder' => "Síntese da sua sugestão"
                                ]) !!}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('telefone', 'Telefone') !!}
                                {!! Form::text('telefone', null, [
                                    'class' => 'form-control isTelefone',
                                    'required'=> false,
                                    'autocomplete' => 'off',
                                    'placeholder' => "Apenas se quiser"
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('descricao', 'Descrição') !!}<small class="text-danger">*</small>
                                {!! Form::textarea('descricao', null, [
                                    'class' => 'form-control',
                                    'required'=> true,
                                    'autocomplete' => 'off',
                                    'rows' => 3
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-paper-plane"></i>
                        Enviar
                    </button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-sugestao"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modal-sugestao-label"
    aria-hidden="true"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-sugestao-label">Minhas Sugestões</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    {!! $dataTable->table(['class' => 'table w-100']) !!}
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

</script>
@endpush
