<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('deficiencia[surdos]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencia[surdos]', 'Sócios Surdos') !!}<br><small class="text-muted">Comunica-se por LIBRAS como primeiro idioma</small>
        {!! Form::number('deficiencia[surdos]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencia[surdos]') }}</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('deficiencia[auditiva]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencia[auditiva]', 'Sócios com Deficiência Auditiva') !!}<br><small class="text-muted">Perda parcial da percepção sonora, faz uso de aparelho auditivo</small>
        {!! Form::number('deficiencia[auditiva]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencia[auditiva]') }}</small>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('deficiencia[cegos]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencia[cegos]', 'Sócios Cegos') !!}<br><small class="text-muted">Perda severa da visão, usa o Sistema Braile, e/ou orientação por voz</small>
        {!! Form::number('deficiencia[cegos]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencia[cegos]') }}</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('deficiencia[baixa_visao]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencia[baixa_visao]', 'Sócios com Baixa Visão') !!}<br><small class="text-muted"> Perda visual profunda ou moderada, necessidade de leitura ampliada, não utiliza Sistema Braile</small>
        {!! Form::number('deficiencia[baixa_visao]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencia[baixa_visao]') }}</small>
        </div>
    </div>
    
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('deficiencia[fisica_inferior]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencia[fisica_inferior]', 'Sócios com deficiência Física/Motora em membros inferiores') !!}
        {!! Form::number('deficiencia[fisica_inferior]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencia[fisica_inferior]') }}</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('deficiencia[fisica_superior]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencia[fisica_superior]', 'Sócios com deficiência Física/Motora em membros superiores') !!}
        {!! Form::number('deficiencia[fisica_superior]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencia[fisica_superior]') }}</small>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('deficiencia[neurologico]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencia[neurologico]', 'Sócios com algum transtorno neurológico') !!}<br><small class="text-muted"> Ex.: Pessoas com TEA (Transtorno do Espectro Autista) / Dislexia / TDAH (Transtorno do Déficit de Atenção com Hiperatividade)</small>
        {!! Form::number('deficiencia[neurologico]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencia[neurologico]') }}</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('deficiencia[intelectual]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencia[intelectual]', 'Sócios com deficiência intelectual') !!}<br><small class="text-muted">Ex.: Pessoas com Síndrome de Down / Síndrome de Angelman</small>
        {!! Form::number('deficiencia[intelectual]', 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('deficiencia[intelectual]') }}</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('deficiencia[outras]') ? ' has-error' : '' }}">
        {!! Form::label('deficiencia[outras]', 'Há sócios com deficiências ou necessidade de acessibilidade não mencionadas?') !!}<br><small class="text-muted">Se sim, descrever qual a deficiência ou necessidade. Se não, responder não.</small>
        {!! Form::text('deficiencia[outras]', null, ['class' => 'form-control']) !!}
        <small class="text-danger">{{ $errors->first('deficiencia[outras]') }}</small>
        </div>
    </div>
</div>