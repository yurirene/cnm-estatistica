@extends('layouts.app')

@section('content')

@include('dashboard.partes.head',[
    'titulo' => 'Federações'
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
                    @if (!isset($federacao))
                    {!! Form::open(['url' => route('dashboard.federacoes.store'), 'method' => 'POST']) !!}
                    @else
                    {!! Form::model($federacao, ['url' => route('dashboard.federacoes.update', $federacao->id), 'method' => 'PUT']) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('nome', 'Nome') !!}
                                {!! Form::text('nome', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off', 'placeholder' => 'Federação']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sigla', 'Sigla') !!}
                                {!! Form::text('sigla', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('status', 'Situação') !!}
                                {!! Form::select('status', ['A' => 'Ativa', 'I' => 'Inativa'], isset($federacao) ? ($federacao->status == true ? ' A' : 'I') : null , ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('estado_id', 'Estado') !!}
                                {!! Form::select('estado_id', $estados, null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('email_usuario', 'E-mail do Usuário') !!}
                                {!! Form::email('email_usuario', isset($federacao) ? FormHelper::getUsarioInstancia($federacao, 'email') : null, ['class' => 'form-control', 'required'=>true, 'readonly' => true]) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('nome_usuario', 'Nome do Usuário') !!}
                                {!! Form::text('nome_usuario', isset($federacao) ? FormHelper::getUsarioInstancia($federacao, 'name') : '-', ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
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
                        @if(count(auth()->user()->sinodais) > 1)
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('sinodal_id', 'Sinodal') !!}
                                {!! Form::select('sinodal_id', auth()->user()->sinodais->pluck('nome', 'id'), null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @else 
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::hidden('sinodal_id', auth()->user()->sinodais()->first()->id ,['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        @endif
                    </div>
                    <button class="btn btn-success"><i class='fas fa-save'></i> {{(isset($federacao) ? 'Atualizar' : 'Cadastrar')}}</button>
                    <a href="{{ route('dashboard.federacoes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('js')

<script>

const sinodal = '{{ auth()->user()->sinodais()->first()->sigla }}'

$('#status').on('change', function() {
    if ($(this).val() == 'I') {
        $('#email_usuario').prop('required', false);
        $('#nome_usuario').prop('required', false);
    } else {
        $('#email_usuario').prop('required', true);
        $('#nome_usuario').prop('required', true);
    }
})

$('#sigla').on('keyup', function() {
    let user = $(this).val().toLowerCase().replaceAll(' ','');
    let email = user + '.' + sinodal.toLowerCase() + '@ump.com';
    $('#email_usuario').val(email);
});

</script>

@endpush