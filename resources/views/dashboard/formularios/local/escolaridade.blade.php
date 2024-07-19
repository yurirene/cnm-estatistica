<div class="row">
    <div class="col-md-3  d-flex flex-column justify-content-end">
        <div class="form-group{{ $errors->has('escolaridade[fundamental]') ? ' has-error' : '' }}">
        {!! Form::label('escolaridade[fundamental]', 'Sócios que têm até o Ensino Fundamental') !!}
        {!! Form::number('escolaridade[fundamental]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('escolaridade[fundamental]') }}</small>
        </div>
    </div>
    <div class="col-md-3  d-flex flex-column justify-content-end">
        <div class="form-group{{ $errors->has('escolaridade[medio]') ? ' has-error' : '' }}">
        {!! Form::label('escolaridade[medio]', 'Sócios que têm até o Ensino Médio') !!}
        {!! Form::number('escolaridade[medio]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('escolaridade[medio]') }}</small>
        </div>
    </div>
    <div class="col-md-3  d-flex flex-column justify-content-end">
        <div class="form-group{{ $errors->has('escolaridade[tecnico]') ? ' has-error' : '' }}">
        {!! Form::label('escolaridade[tecnico]', 'Sócios que têm até o Ensino Técnico') !!}
        {!! Form::number('escolaridade[tecnico]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('escolaridade[tecnico]') }}</small>
        </div>
    </div>
    <div class="col-md-3  d-flex flex-column justify-content-end">
        <div class="form-group{{ $errors->has('escolaridade[superior]') ? ' has-error' : '' }}">
        {!! Form::label('escolaridade[superior]', 'Sócios que têm até o Ensino Superior') !!}
        {!! Form::number('escolaridade[superior]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('escolaridade[superior]') }}</small>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3  d-flex flex-column justify-content-end">
        <div class="form-group{{ $errors->has('escolaridade[pos]') ? ' has-error' : '' }}">
        {!! Form::label('escolaridade[pos]', 'Sócios que têm até a Pós-Graduação') !!}
        {!! Form::number('escolaridade[pos]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('escolaridade[pos]') }}</small>
        </div>
    </div>
</div>
