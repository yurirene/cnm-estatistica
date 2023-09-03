<div class="row">
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[ativos]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[ativos]', 'Sócios Ativos') !!}
        {!! Form::number('perfil[ativos]', isset($formulario) ? null : 0,
            ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[ativos]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[cooperadores]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[cooperadores]', 'Sócios Cooperadores') !!}
        {!! Form::number('perfil[cooperadores]', isset($formulario) ? null : 0,
            ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[cooperadores]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[menor19]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[menor19]', 'Sócios menores de 19 anos') !!}
        {!! Form::number('perfil[menor19]', isset($formulario) ? null : 0,
            ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[menor19]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[de19a23]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[de19a23]', 'Sócios entre 19-23 anos') !!}
        {!! Form::number('perfil[de19a23]', isset($formulario) ? null : 0,
            ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[de19a23]') }}</small>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[de24a29]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[de24a29]', 'Sócios entre 24-29 anos') !!}
        {!! Form::number('perfil[de24a29]', isset($formulario) ? null : 0,
            ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[de24a29]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[de30a35]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[de30a35]', 'Sócios entre 30-35 anos') !!}
        {!! Form::number('perfil[de30a35]', isset($formulario) ? null : 0,
            ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[de30a35]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[homens]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[homens]', 'Sócios - Homens') !!}
        {!! Form::number('perfil[homens]', isset($formulario) ? null : 0,
            ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[homens]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[mulheres]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[mulheres]', 'Sócios - Mulheres') !!}
        {!! Form::number('perfil[mulheres]', isset($formulario) ? null : 0,
            ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[mulheres]') }}</small>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-3  d-flex flex-column justify-content-end">
        <div class="form-group{{ $errors->has('escolaridade[desempregado]') ? ' has-error' : '' }}">
        {!! Form::label('escolaridade[desempregado]', 'Sócios desempregados') !!}
        {!! Form::number('escolaridade[desempregado]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('escolaridade[desempregado]') }}</small>
        </div>
    </div>
</div>
