<div class="row">
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('organizacao[treinamentos_participados_federacao]') ? ' has-error' : '' }}">
        {!! Form::label('organizacao[treinamentos_participados_federacao]', 'Quantidade de treinamentos participados da Federação') !!}
        {!! Form::number('organizacao[treinamentos_participados_federacao]', isset($formulario) && !empty($formulario->organizacao) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('organizacao[treinamentos_participados_federacao]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('organizacao[treinamentos_participados_sinodal]') ? ' has-error' : '' }}">
        {!! Form::label('organizacao[treinamentos_participados_sinodal]', 'Quantidade de treinamentos participados da Sinodal') !!}
        {!! Form::number('organizacao[treinamentos_participados_sinodal]', isset($formulario) && !empty($formulario->organizacao) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('organizacao[treinamentos_participados_sinodal]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('organizacao[treinamentos_participados_cnm]') ? ' has-error' : '' }}">
        {!! Form::label('organizacao[treinamentos_participados_cnm]', 'Quantidade de treinamentos participados da CNM') !!}
        {!! Form::number('organizacao[treinamentos_participados_cnm]', isset($formulario) && !empty($formulario->organizacao) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('organizacao[treinamentos_participados_cnm]') }}</small>
        </div>
    </div>
</div>
