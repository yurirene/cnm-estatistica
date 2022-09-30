<div class="row">
    <div class="col-md-6  d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('deficiencias[surdos]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencias[surdos]', 'Sócios Surdos') !!}<br><small class="text-muted">Comunica-se por LIBRAS como primeiro idioma</small>
        {!! Form::number('deficiencias[surdos]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencias[surdos]') }}</small>
        </div>
    </div>
    <div class="col-md-6  d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('deficiencias[auditiva]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencias[auditiva]', 'Sócios com Deficiência Auditiva') !!}<br><small class="text-muted">Perda parcial da percepção sonora, faz uso de aparelho auditivo</small>
        {!! Form::number('deficiencias[auditiva]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencias[auditiva]') }}</small>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6  d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('deficiencias[cegos]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencias[cegos]', 'Sócios Cegos') !!}<br><small class="text-muted">Perda severa da visão, usa o Sistema Braile, e/ou orientação por voz</small>
        {!! Form::number('deficiencias[cegos]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencias[cegos]') }}</small>
        </div>
    </div>
    <div class="col-md-6  d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('deficiencias[baixa_visao]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencias[baixa_visao]', 'Sócios com Baixa Visão') !!}<br><small class="text-muted"> Perda visual profunda ou moderada, necessidade de leitura ampliada, não utiliza Sistema Braile</small>
        {!! Form::number('deficiencias[baixa_visao]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencias[baixa_visao]') }}</small>
        </div>
    </div>
    
</div>
<div class="row">
    <div class="col-md-6  d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('deficiencias[fisica_inferior]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencias[fisica_inferior]', 'Sócios com deficiência Física/Motora em membros inferiores') !!}
        {!! Form::number('deficiencias[fisica_inferior]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencias[fisica_inferior]') }}</small>
        </div>
    </div>
    <div class="col-md-6  d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('deficiencias[fisica_superior]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencias[fisica_superior]', 'Sócios com deficiência Física/Motora em membros superiores') !!}
        {!! Form::number('deficiencias[fisica_superior]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencias[fisica_superior]') }}</small>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6  d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('deficiencias[neurologico]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencias[neurologico]', 'Sócios com algum transtorno neurológico') !!}<br><small class="text-muted"> Ex.: Pessoas com TEA (Transtorno do Espectro Autista) / Dislexia / TDAH (Transtorno do Déficit de Atenção com Hiperatividade)</small>
        {!! Form::number('deficiencias[neurologico]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencias[neurologico]') }}</small>
        </div>
    </div>
    <div class="col-md-6  d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('deficiencias[intelectual]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencias[intelectual]', 'Sócios com deficiência intelectual') !!}<br><small class="text-muted">Ex.: Pessoas com Síndrome de Down / Síndrome de Angelman</small>
        {!! Form::number('deficiencias[intelectual]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencias[intelectual]') }}</small>
        </div>
    </div>
</div>
@if(!isset($export))
<div class="row">
    <div class="col-md-6  d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('deficiencias[outras]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencias[outras]', 'Há sócios com deficiências ou necessidade de acessibilidade não mencionadas?') !!}<br><small class="text-muted">Se sim, descrever qual a deficiência ou necessidade. Se não, responder não.</small>
        {!! Form::text('deficiencias[outras]', null, ['class' => 'form-control']) !!}
        <small class="text-danger">{{ $errors->first('deficiencias[outras]') }}</small>
        </div>
    </div>
</div>
@endif