@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Sinodais'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Formulário</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (!isset($sinodal))
                    {!! Form::open(['url' => route('dashboard.sinodais.store'), 'method' => 'POST']) !!}
                    @else
                    {!! Form::model($sinodal, ['url' => route('dashboard.sinodais.update', $sinodal->id), 'method' => 'PUT']) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('nome', 'Nome') !!}
                                {!! Form::text('nome', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off', 'placeholder' => 'Confederação Sinodal de Mocidades']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sigla', 'Sigla') !!}
                                {!! Form::text('sigla', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off', 'placeholder' => 'CSM']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('status', 'Situação') !!}
                                {!! Form::select('status', ['A' => 'Ativa', 'I' => 'Inativa'],  isset($sinodal) ? ($sinodal->status == true ? ' A' : 'I') : null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('email_usuario', 'E-mail do Usuário') !!}
                                {!! Form::email('email_usuario', isset($sinodal) ? FormHelper::getUsarioInstancia($sinodal, 'email') : null, ['class' => 'form-control', 'required'=>true, 'readonly' => true]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mt-5">
                                <div class="checkbox{{ $errors->has('resetar_senha') ? ' has-error' : '' }}">
                                    <label for="resetar_senha">
                                    {!! Form::checkbox('resetar_senha', '1', null, ['id' => 'resetar_senha']) !!} Resetar Senha
                                    </label>
                                </div>
                            </div>
                        </div>
                        @if (auth()->user()->admin)
                            <div class="form-group">
                                {!! Form::label('regiao_id', 'Região') !!}
                                {!! Form::select('regiao_id', $regioes, null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        @else
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::hidden('regiao_id', auth()->user()->regiao()->first()->id ,['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                    {!! Form::hidden('nome_usuario', 'Sinodal') !!}

                    <button class="btn btn-success"><i class='fas fa-save'></i> {{(isset($sinodal) ? 'Atualizar' : 'Cadastrar')}}</button>
                    <a href="{{ route('dashboard.sinodais.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')

<script>

const regiao = '{{$regiao}}'


$('#status').on('change', function() {
    if ($(this).val() == 'I') {
        $('#email_usuario').prop('required', false);
        $('#nome_usuario').prop('required', false);
    } else {
        $('#email_usuario').prop('required', true);
        $('#nome_usuario').prop('required', true);
    }
});

$('#sigla').on('keyup', function() {
    let user = $(this).val().toLowerCase().replaceAll(' ','');
    let email = user + '.' + regiao + '@ump.com';
    $('#email_usuario').val(email);
});


</script>

@endpush
