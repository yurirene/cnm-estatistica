<div class="row">
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('discipulado[trilha_cnm]') ? ' has-error' : '' }}">
        {!! Form::label('discipulado[trilha_cnm]', 'Quantidade de jovens que fizeram a trilha da CNM') !!}
        {!! Form::number('discipulado[trilha_cnm]', isset($formulario) && !empty($formulario->discipulado) ? null : 0, ['class' => 'form-control', 'required' => 'required', 'readonly' => 'readonly']) !!}
        <small class="text-danger">{{ $errors->first('discipulado[trilha_cnm]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('discipulado[discipulando_cnm]') ? ' has-error' : '' }}">
        {!! Form::label('discipulado[discipulando_cnm]', 'Quantidade de jovens discipulando pelo método da CNM') !!}
        {!! Form::number('discipulado[discipulando_cnm]', isset($formulario) && !empty($formulario->discipulado) ? null : 0, ['class' => 'form-control', 'required' => 'required', 'readonly' => 'readonly']) !!}
        <small class="text-danger">{{ $errors->first('discipulado[discipulando_cnm]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('discipulado[discipulando_outro]') ? ' has-error' : '' }}">
        {!! Form::label('discipulado[discipulando_outro]', 'Quantidade de jovens discipulando por outro método') !!}
        {!! Form::number('discipulado[discipulando_outro]', isset($formulario) && !empty($formulario->discipulado) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('discipulado[discipulando_outro]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('discipulado[sendo_discipulados]') ? ' has-error' : '' }}">
        {!! Form::label('discipulado[sendo_discipulados]', 'Quantidade de jovens sendo discipulados') !!}
        {!! Form::number('discipulado[sendo_discipulados]', isset($formulario) && !empty($formulario->discipulado) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('discipulado[sendo_discipulados]') }}</small>
        </div>
    </div>
</div>
