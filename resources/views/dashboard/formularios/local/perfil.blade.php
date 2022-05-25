<div class="row">
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[ativos]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[ativos]', 'Sócios Ativos') !!}
        {!! Form::number('perfil[ativos]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[ativos]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[cooperadores]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[cooperadores]', 'Sócios Cooperadores') !!}
        {!! Form::number('perfil[cooperadores]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[cooperadores]') }}</small>
        </div>  
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[menor19]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[menor19]', 'Sócios menores de 19 anos') !!}
        {!! Form::number('perfil[menor19]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[menor19]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[19a23]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[19a23]', 'Sócios entre 19-23 anos') !!}
        {!! Form::number('perfil[19a23]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[19a23]') }}</small>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[24a29]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[24a29]', 'Sócios entre 24-29 anos') !!}
        {!! Form::number('perfil[24a29]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[24a29]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[30-35]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[30-35]', 'Sócios entre 30-35') !!}
        {!! Form::number('perfil[30-35]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[30-35]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[homens]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[homens]', 'Sócios - Homens') !!}
        {!! Form::number('perfil[homens]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[homens]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('perfil[mulheres]') ? ' has-error' : '' }}">
        {!! Form::label('perfil[mulheres]', 'Sócios - Mulheres') !!}
        {!! Form::number('perfil[mulheres]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('perfil[mulheres]') }}</small>
        </div>
    </div>
</div>