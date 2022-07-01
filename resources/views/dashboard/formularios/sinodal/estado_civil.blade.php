<div class="row">
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('estado_civil[solteiros]') ? ' has-error' : '' }}">
        {!! Form::label('estado_civil[solteiros]', 'Sócios Solteiros') !!}
        {!! Form::number('estado_civil[solteiros]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estado_civil[solteiros]') }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('estado_civil[casados]') ? ' has-error' : '' }}">
        {!! Form::label('estado_civil[casados]', 'Sócios Casados') !!}
        {!! Form::number('estado_civil[casados]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estado_civil[casados]') }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('estado_civil[divorciados]') ? ' has-error' : '' }}">
        {!! Form::label('estado_civil[divorciados]', 'Sócios Divorciados') !!}
        {!! Form::number('estado_civil[divorciados]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estado_civil[divorciados]') }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('estado_civil[viuvos]') ? ' has-error' : '' }}">
        {!! Form::label('estado_civil[viuvos]', 'Sócios Viúvos') !!}
        {!! Form::number('estado_civil[viuvos]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estado_civil[viuvos]') }}</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('estado_civil[filhos]') ? ' has-error' : '' }}">
        {!! Form::label('estado_civil[filhos]', 'Sócios com filhos') !!}
        {!! Form::number('estado_civil[filhos]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estado_civil[filhos]') }}</small>
        </div>
    </div>
</div>
