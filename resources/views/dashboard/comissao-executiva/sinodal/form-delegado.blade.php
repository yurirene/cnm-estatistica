<h4>Delegado @if(!empty($delegado)) {!! $delegado->status_formatado !!} @endif</h4>
@if (empty($delegado))
    {!! Form::open([
            'method' => 'POST',
            'route' => 'dashboard.ce-sinodal.delegado.store',
            'files' => true
        ]) !!}
@else
    {!! Form::model(
            $delegado,
            [
                'url' => route('dashboard.ce-sinodal.delegado.update', $delegado->id),
                'method' => 'PUT',
                'files' => true
            ]
        ) !!}
@endif

<div class="row">
    <div class="col-md-4 mt-2">
        <div class="form-group">
            {!! Form::label('nome', 'Nome do Delegado') !!}
            {!! Form::text(
                'nome',
                null,
                [
                    'class' => 'form-control',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => 'Digite o nome completo do delegado',
                    'readonly' => !empty($delegado) && $delegado->credencial == 1
                ]
            ) !!}
        </div>
    </div>
    <div class="col-md-3 mt-2">
        <div class="form-group">
            {!! Form::label('cpf', 'CPF') !!}
            {!! Form::text(
                'cpf',
                null,
                [
                    'class' => 'form-control isCpf',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => '000.000.000-00',
                    'readonly' => !empty($delegado) && $delegado->credencial == 1
                ]
            ) !!}
        </div>
    </div>
    <div class="col-md-3 mt-2">
        <div class="form-group">
            {!! Form::label('cpf', 'Telefone') !!}
            {!! Form::text(
                'telefone',
                null,
                [
                    'class' => 'form-control isTelefone',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => '(99) 99999-9999',
                    'readonly' => !empty($delegado) && $delegado->credencial == 1
                ]
            ) !!}
        </div>
    </div>
    <div class="col-md-2 mt-2">
        <div class="form-group">
            {!! Form::label('cpf', 'Oficial') !!}
            {!! Form::select(
                'oficial',
                [
                    '0' => 'Não',
                    '1' => 'Diácono',
                    '2' => 'Presbítero'
                ],
                null,
                [
                    'class' => 'form-control',
                    'required' => true,
                    'autocomplete' => 'off',
                    'readonly' => !empty($delegado) && $delegado->credencial == 1
                ]
            ) !!}
        </div>
    </div>
</div>
@if((!empty($delegado) && $delegado->credencial == 0) || empty($delegado))
    <div class="row">
        <div class="col-md-12 mt-2">
            <div class="form-group">
                {!! Form::label('credencial', 'Credencial') !!}
                {!! Form::file(
                    'credencial',
                    [
                        'class' => 'form-control-file',
                        'accept' => 'application/pdf,image/*',
                        'required' => empty($delegado)
                    ]
                ) !!}
                <small class="form-text text-muted">
                    Aceita arquivos PDF e imagens (JPG, PNG, etc.)
                </small>
                @if(!empty($delegado))
                    <a href="/{{ $delegado->path_credencial }}" target="_blank">
                        <i class="fas fa-eye"></i> Ver credencial
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <button class="btn btn-success">
                    {{ !empty($delegado) ? 'Atualizar Delegado' : 'Cadastrar Delegado' }}
                </button>
            </div>
        </div>
    </div>

@endif

@if(!empty($delegado) && $delegado->credencial == 1)
    <a href="/{{ $delegado->path_credencial }}" target="_blank">
        <i class="fas fa-eye"></i> Ver credencial
    </a>
@endif
{!! Form::close() !!}

<hr>
<h4>Suplente @if(!empty($suplente)) {!! $suplente->status_formatado !!} @endif </h4>
@if (empty($suplente))
    {!! Form::open([
            'method' => 'POST',
            'route' => 'dashboard.ce-sinodal.delegado.store',
            'files' => true
        ]) !!}
@else
    {!! Form::model(
            $suplente,
            [
                'url' => route('dashboard.ce-sinodal.delegado.update', $suplente->id),
                'method' => 'PUT',
                'files' => true
            ]
        ) !!}
@endif
<input type="hidden" name="suplente" value="1">
<div class="row">
    <div class="col-md-6 mt-2">
        <div class="form-group">
            {!! Form::label('nome', 'Nome do Suplente') !!}
            {!! Form::text(
    'nome',
    null,
    [
        'class' => 'form-control',
        'required' => true,
        'autocomplete' => 'off',
        'placeholder' => 'Digite o nome completo do suplente',
        'readonly' => !empty($suplente) && $suplente->credencial == 1
    ]
) !!}
        </div>
    </div>
    <div class="col-md-6 mt-2">
        <div class="form-group">
            {!! Form::label('cpf', 'CPF') !!}
            {!! Form::text(
    'cpf',
    null,
    [
        'class' => 'form-control isCpf',
        'required' => true,
        'autocomplete' => 'off',
        'placeholder' => '000.000.000-00',
        'readonly' => !empty($suplente) && $suplente->credencial == 1
    ]
) !!}
        </div>
    </div>
</div>

@if((!empty($suplente) && $suplente->credencial == 0) || empty($suplente))
    <div class="row">
        <div class="col-md-12 mt-2">
            <div class="form-group">
                {!! Form::label('credencial', 'Credencial') !!}
                {!! Form::file(
            'credencial',
            [
                'class' => 'form-control-file',
                'accept' => 'application/pdf,image/*',
                'required' => empty($suplente)
            ]
        ) !!}
                <small class="form-text text-muted">
                    Aceita arquivos PDF e imagens (JPG, PNG, etc.)
                </small>

                @if(!empty($suplente))
                    <a href="/{{ $suplente->path_credencial }}" target="_blank">
                        <i class="fas fa-eye"></i> Ver credencial
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <button class="btn btn-success">
                    {{ !empty($suplente) ? 'Atualizar Suplente' : 'Cadastrar Suplente' }}
                </button>
            </div>
        </div>
    </div>

@endif

@if(!empty($delegado) && $delegado->credencial == 1)
    <a href="/{{ $delegado->path_credencial }}" target="_blank">
        <i class="fas fa-eye"></i> Ver credencial
    </a>
@endif
{!! Form::close() !!}