@if(!empty($delegado->id))
    {!! Form::model(
        $delegado,
        [
            'url' => route('dashboard.cn.sinodal.delegado.update', $delegado->id),
            'method' => 'PUT',
            'files' => true
        ]
    ) !!}
@else
    {!! Form::open([
        'url' => route('dashboard.cn.sinodal.delegado.store'),
        'method' => 'POST',
        'files' => true
    ]) !!}
@endif

<div class="row">
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
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('cpf', 'CPF') !!}
            {!! Form::text(
                'cpf',
                null,
                [
                    'class' => 'form-control',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => 'Digite o CPF'
                ]
            ) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('telefone', 'Telefone') !!}
            {!! Form::text(
                'telefone',
                null,
                [
                    'class' => 'form-control isTelefone',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => '(99) 99999-9999'
                ]
            ) !!}
        </div>
    </div>
    <div class="col-md-2">
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
                    'readonly' => !empty($delegado->id) && $delegado->credencial == 1
                ]
            ) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('credencial_file', 'Credencial (PDF ou Imagem)') !!}
            {!! Form::file(
                'credencial_file',
                [
                    'class' => 'form-control',
                    'accept' => 'application/pdf,image/*'
                ]
            ) !!}
            <small class="form-text text-muted">Apenas para novos uploads. Formatos aceitos: PDF, JPG, PNG (máx. 2MB)</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 ms-5">
        <div class="form-check form-switch">
            <input class="form-check-input check-custom" type="checkbox" role="switch" id="pago" name="pago" value="1" {{ !empty($delegado->id) && $delegado->pago ? 'checked' : '' }}>
            <label class="form-check-label" for="pago">Pago</label>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input check-custom" type="checkbox" role="switch" id="credencial" name="credencial" value="1" {{ !empty($delegado->id) && $delegado->credencial ? 'checked' : '' }}>
            <label class="form-check-label" for="credencial">Credencial</label>
        </div>
    </div>

    @if(!empty($delegado->path_credencial))
        <div class="col-md-6">
            <a href="/{{ $delegado->path_credencial }}" target="_blank" class="link mt-3">
                <i class="fas fa-eye"></i> Ver credencial atual
            </a>
        </div>
    @endif
</div>
<div class="row mt-3">
    <div class="col-md-6">
        <div class="form-group">
            <button class="btn btn-success">
                {{ !empty($delegado->id) ? 'Atualizar Delegado' : 'Cadastrar Delegado' }}
            </button>
            <a href="{{ route('dashboard.cn.sinodal.index') }}" class="btn btn-secondary">
                Cancelar
            </a>
        </div>
    </div>
</div>
{!! Form::close() !!}
