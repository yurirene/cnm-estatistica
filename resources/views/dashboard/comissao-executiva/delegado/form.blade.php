{!! Form::model(
    $delegado,
    [
        'url' => route('dashboard.comissao-executiva.delegado.update', $delegado->id),
        'method' => 'PUT',
        'files' => true
    ]
) !!}

<div class="row">
    <div class="col-md-4">
        {!! Form::label('status', 'Status') !!}
        {!! Form::select(
            'status',
            $status,
            null,
            [
                'class' => 'form-control',
                'required' => true,
            ]
        ) !!}
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('nome', 'Nome') !!}
            {!! Form::text(
                'nome',
                null,
                [
                    'class' => 'form-control',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => 'Digite o nome completo'
                ]
            ) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('cpf', 'CPF') !!}
            {!! Form::text(
                'cpf',
                null,
                [
                    'class' => 'form-control isCpf',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => '000.000.000-00'
                ]
            ) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 ms-5">
        <div class="form-check form-switch">
            <input class="form-check-input check-custom" type="checkbox" role="switch" id="pago" name="pago" value="1" {{ $delegado->pago ? 'checked' : '' }}>
            <label class="form-check-label" for="pago">Pago</label>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input check-custom" type="checkbox" role="switch" id="credencial" name="credencial" value="1" {{ $delegado->credencial ? 'checked' : '' }}>
            <label class="form-check-label" for="credencial">Credencial</label>
        </div>
    </div>

    <a href="/{{ $delegado->path_credencial }}" target="_blank" class="link mt-3">
        <i class="fas fa-eye"></i> Ver credencial
    </a>
</div>
<div class="row mt-3">
    <div class="col-md-6">
        <div class="form-group">
            <button class="btn btn-success">
                {{ !empty($delegado) ? 'Atualizar Delegado' : 'Cadastrar Delegado' }}
            </button>
        </div>
    </div>
</div>
{!! Form::close() !!}
