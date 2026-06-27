@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'UMP Local'
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
                    @if (!isset($local))
                    {!! Form::open(['url' => route('dashboard.locais.store'), 'method' => 'POST']) !!}
                    @else
                    {!! Form::model($local, ['url' => route('dashboard.locais.update', $local->id), 'method' => 'PUT']) !!}
                    @endif
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('nome', 'Nome') !!}
                                {!! Form::text('nome', null, ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off', 'placeholder' => 'UMP Igreja']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('status', 'Situação') !!}
                                {!! Form::select('status', ['A' => 'Ativa', 'I' => 'Inativa'], isset($local) ? ($local->status == true ? ' A' : 'I') : null , ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>                        
                        {!! Form::hidden('federacao_id', auth()->user()->federacao_id) !!}
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('email_usuario', 'E-mail do Usuário') !!}
                                {!! Form::email('email_usuario', isset($local) ? FormHelper::getUsarioInstancia($local, 'email') : null, ['class' => 'form-control', 'required'=>true, 'readonly' => true]) !!}
                                <small id="resposta_email"></small>
                            </div>
                        </div>
                        @if(isset($local))
                        <div class="col-md-4">
                            <div class="form-group mt-5">
                                <div class="checkbox{{ $errors->has('resetar_senha') ? ' has-error' : '' }}">
                                    <label for="resetar_senha">
                                    {!! Form::checkbox('resetar_senha', '1', null, ['id' => 'resetar_senha']) !!} Resetar Senha
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group mt-5">
                                <label for="resetar_senha">
                                {!! Form::checkbox('outro_modelo', '1', null, ['id' => 'outro_modelo']) !!} Não trabalha com modelo UMP
                            </div>
                        </div>
                    </div>
                    {!! Form::hidden('nome_usuario', 'UMP Local') !!}
                    <button class="btn btn-success" id="submit-button"><i class='fas fa-save'></i> {{(isset($local) ? 'Atualizar' : 'Cadastrar')}}</button>
                    <a href="{{ route('dashboard.locais.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

    </div>
</div>
<input hidden id="isNovo" value="{{ isset($local) ? 'true' : 'false'}}"/>
<input hidden id="idUsuario" value="{{ isset($local) ? FormHelper::getUsarioInstancia($local, 'id') : null}}"/>
<input hidden id="token" value="{{ csrf_token() }}"/>
@endsection

@push('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js"></script>
<script>


const isNovo = document.getElementById('isNovo').value === "true";
const idUsuario = document.getElementById('idUsuario').value;

$('#nome').keyup($.debounce(500, function(e) {
    verificarUsuario($('#email_usuario').val());
}));

function verificarUsuario(email) {
    $.ajax({
        url: "{{route('dashboard.usuarios.check-usuario')}}",
        type: 'POST',
        data: {
            _token: document.querySelector('#token').value,
            isNovo: isNovo,
            idUsuario: idUsuario,
            email: email
        }
    }).done((response) => {
        let span = document.querySelector('#resposta_email');
        let btn = document.querySelector("#submit-button");
        span.classList.add("text-danger");
        btn.disabled = true;
        if (response.status) {
            span.classList.remove("text-danger");
            span.classList.add("text-success");
            btn.disabled = false;
        }
        span.textContent = response.msg;
    });
}
</script>
<script>
$('#status').on('change', function() {
    if ($(this).val() == 'I') {
        $('#email_usuario').prop('required', false);
        $('#nome_usuario').prop('required', false);
    } else {
        $('#email_usuario').prop('required', true);
        $('#nome_usuario').prop('required', true);
    }
})

const federacao = '{{ auth()->user()->federacao->sigla }}'

$('#nome').on('keyup', function() {
    let user = $(this).val().normalize("NFD").replace(/\p{Diacritic}/gu, "").split(' ').join('').toLowerCase();
    let email = user + '.' + federacao.toLowerCase().replaceAll(' ','') + '@ump.com';
    $('#email_usuario').val(email);
});



</script>

@endpush
