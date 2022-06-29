
{!! Form::open(['method' => 'POST', 'route' => 'dashboard.comprovante-aci.store', 'files' => true]) !!}
<div class="row">
    
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('ano', 'Ano') !!}
            {!! Form::text('ano', date('Y'), ['class' => 'form-control', 'required'=>true, 'autocomplete' => 'off', 'readonly' => true]) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('arquivo') ? ' has-error' : '' }}">
            {!! Form::label('arquivo', 'Comprovante') !!}
            {!! Form::file('arquivo', ['required' => 'required', 'class' => 'form-control']) !!}
            <p class="help-block">Selecione o arquivo</p>
            <small class="text-danger">{{ $errors->first('arquivo') }}</small>
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
