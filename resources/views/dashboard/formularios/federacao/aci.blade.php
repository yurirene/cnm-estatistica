<div class="row">
    <div class="col-md-4 col-sm-6 mt-3 d-flex flex-column justify-content-end ">
        <div class="form-group{{ $errors->has('aci[repasse]') ? ' has-error' : '' }}">
        {!! Form::label('aci[repasse]', 'A Federação fez o repasse da ACI para a Sinodal') !!}
        {!! Form::select('aci[repasse]',['S' => 'Sim', 'N' => 'Não'], null, ['id' => 'aci[repasse]', 'class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('aci[repasse]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('aci[valor]') ? ' has-error' : '' }}">
        {!! Form::label('aci[valor]', 'Valor repassado') !!}
        {!! Form::text('aci[valor]', isset($formulario) ? null : 0, ['class' => 'form-control isMoney', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('aci[valor]') }}</small>
        </div>  
    </div>
</div>