<div class="row">
    <div class="col-md-3 d-flex flex-column justify-content-end ">
        <div class="form-group{{ $errors->has('programacoes[social]') ? ' has-error' : '' }}">
        {!! Form::label(
            'programacoes[social]',
            'Programações de cunho social realizadas'
        ) !!}
        <br><small class="text-muted">Ex: Entrega de cestas básicas, visita a orfanatos, incluindo os projetos da Secretaria de Responsabilidade Social</small>
        {!! Form::number(
            'programacoes[social]',
            isset($formulario) && !empty($formulario->programacoes)
                ? null
                : 0,
            ['class' => 'form-control', 'required' => 'required']
        ) !!}
        <small class="text-danger">{{ $errors->first('programacoes[social]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end ">
        <div class="form-group{{ $errors->has('programacoes[evangelistico]') ? ' has-error' : '' }}">
        {!! Form::label(
            'programacoes[evangelistico]',
            'Programações de cunho evangelístico e missional realizadas'
        ) !!}
        <br><small class="text-muted">Ex: Viagem missionária, culto em praças, distribuição de folhetos, incluindo os projetos da Secretaria de Evangelismo e Missões.</small>
        {!! Form::number(
            'programacoes[evangelistico]',
            isset($formulario) && !empty($formulario->programacoes)
                ? null
                : 0,
            ['class' => 'form-control', 'required' => 'required']
        ) !!}
        <small class="text-danger">{{ $errors->first('programacoes[evangelistico]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end ">
        <div class="form-group{{ $errors->has('programacoes[espiritual]') ? ' has-error' : '' }}">
        {!! Form::label(
            'programacoes[espiritual]',
            'Programações de cunho espiritual realizadas'
        ) !!}
        <br><small class="text-muted">Ex: Estudo bíblico / Estudo de livro / Pequenos grupos / Cultos</small>
        {!! Form::number(
            'programacoes[espiritual]',
            isset($formulario) && !empty($formulario->programacoes)
                ? null
                : 0,
            ['class' => 'form-control', 'required' => 'required']
        ) !!}
        <small class="text-danger">{{ $errors->first('programacoes[espiritual]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end ">
        <div class="form-group{{ $errors->has('programacoes[recreativo]') ? ' has-error' : '' }}">
        {!! Form::label(
            'programacoes[recreativo]',
            'Programações de cunho recreativo realizadas'
        ) !!}
        <br><small class="text-muted">Ex: Gincanas / Torneio / Passeios / Piquenique</small>
        {!! Form::number(
            'programacoes[recreativo]',
            isset($formulario) && !empty($formulario->programacoes)
                ? null
                : 0,
            ['class' => 'form-control', 'required' => 'required']
        ) !!}
        <small class="text-danger">{{ $errors->first('programacoes[recreativo]') }}</small>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3 d-flex flex-column justify-content-end ">
        <div class="form-group{{ $errors->has('programacoes[oracao]') ? ' has-error' : '' }}">
            {!! Form::label(
                'programacoes[oracao]',
                'Programações de Reuniões de Oração e Vigílias'
            ) !!}
            {!! Form::number(
                'programacoes[oracao]',
                isset($formulario) && !empty($formulario->programacoes)
                    ? null
                    : 0,
                ['class' => 'form-control', 'required' => 'required']
            ) !!}
            <small class="text-danger">{{ $errors->first('programacoes[oracao]') }}</small>
            </div>
    </div>
</div>
