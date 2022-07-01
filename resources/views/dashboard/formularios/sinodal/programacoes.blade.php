<h3>Sinodal</h3>
<div class="row">
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('programacoes[social]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes[social]', 'Programações de cunho social realizadas pela Sinodal') !!}<small class="text-muted">Incluindo os projetos da Secretaria de Responsabilidade Social</small>
        {!! Form::number('programacoes[social]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes[social]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('programacoes[evangelistico]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes[evangelistico]', 'Programações de cunho evangelístico e missional realizadas pela Sinodal') !!} <br><small class="text-muted">Incluindo os projetos da Secretaria de Evangelismo e Missões.</small>
        {!! Form::number('programacoes[evangelistico]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes[evangelistico]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('programacoes[espiritual]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes[espiritual]', 'Programações de cunho espiritual realizadas pela Sinodal') !!} <br><small class="text-muted">Estudo bíblico / Estudo de livro / Pequenos grupos / Cultos</small>
        {!! Form::number('programacoes[espiritual]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes[espiritual]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('programacoes[recreativo]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes[recreativo]', 'Programações de cunho recreativo realizadas pela Sinodal') !!} <br><small class="text-muted">Gincanas / Torneio / Passeios / Piquenique</small>
        {!! Form::number('programacoes[recreativo]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes[recreativo]') }}</small>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('programacoes[oracao]') ? ' has-error' : '' }}">
            {!! Form::label('programacoes[oracao]', 'Programações de Reuniões de Oração e Vigílias realizadas pela Sinodal') !!}
            {!! Form::number('programacoes[oracao]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
            <small class="text-danger">{{ $errors->first('programacoes[oracao]') }}</small>
            </div>
    </div>
</div>

<h3>Federação</h3>
<div class="row">
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('programacoes_federacoes[social]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes_federacoes[social]', 'Programações de cunho social realizadas pelas Federações') !!} <br><small class="text-muted">Incluindo os projetos da Secretaria de Responsabilidade Social</small>
        {!! Form::number('programacoes_federacoes[social]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes_federacoes[social]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('programacoes_federacoes[evangelistico]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes_federacoes[evangelistico]', 'Programações de cunho evangelístico e missional realizadas pelas Federações') !!} <br><small class="text-muted">Incluindo os projetos da Secretaria de Evangelismo e Missões.</small>
        {!! Form::number('programacoes_federacoes[evangelistico]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes_federacoes[evangelistico]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('programacoes_federacoes[espiritual]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes_federacoes[espiritual]', 'Programações de cunho espiritual realizadas pelas Federações') !!} <br><small class="text-muted">Estudo bíblico / Estudo de livro / Pequenos grupos / Cultos</small>
        {!! Form::number('programacoes_federacoes[espiritual]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes_federacoes[espiritual]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('programacoes_federacoes[recreativo]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes_federacoes[recreativo]', 'Programações de cunho recreativo realizadas pelas Federações') !!} <br><small class="text-muted">Gincanas / Torneio / Passeios / Piquenique</small>
        {!! Form::number('programacoes_federacoes[recreativo]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes_federacoes[recreativo]') }}</small>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('programacoes_federacoes[oracao]') ? ' has-error' : '' }}">
            {!! Form::label('programacoes_federacoes[oracao]', 'Programações de Reuniões de Oração e Vigílias realizadas  pelas Federações') !!}
            {!! Form::number('programacoes_federacoes[oracao]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
            <small class="text-danger">{{ $errors->first('programacoes_federacoes[oracao]') }}</small>
            </div>
    </div>
</div>

<h3>UMP Local</h3>
<div class="row">
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="{{ $errors->has('programacoes_locais[social]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes_locais[social]', 'Programações de cunho social realizadas pelas UMPs Locais') !!}<small class="text-muted">Incluindo os projetos da Secretaria de Responsabilidade Social</small>
        {!! Form::number('programacoes_locais[social]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes_locais[social]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="{{ $errors->has('programacoes_locais[evangelistico]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes_locais[evangelistico]', 'Programações de cunho evangelístico e missional realizadas pelas UMPs Locais') !!}<small class="text-muted">Incluindo os projetos da Secretaria de Evangelismo e Missões.</small>
        {!! Form::number('programacoes_locais[evangelistico]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes_locais[evangelistico]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="{{ $errors->has('programacoes_locais[espiritual]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes_locais[espiritual]', 'Programações de cunho espiritual realizadas pelas UMPs Locais') !!}<small class="text-muted">Estudo bíblico / Estudo de livro / Pequenos grupos / Cultos</small>
        {!! Form::number('programacoes_locais[espiritual]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes_locais[espiritual]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="{{ $errors->has('programacoes_locais[recreativo]') ? ' has-error' : '' }}">
        {!! Form::label('programacoes_locais[recreativo]', 'Programações de cunho recreativo realizadas pelas UMPs Locais') !!}<small class="text-muted">Gincanas / Torneio / Passeios / Piquenique</small>
        {!! Form::number('programacoes_locais[recreativo]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('programacoes_locais[recreativo]') }}</small>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('programacoes_locais[oracao]') ? ' has-error' : '' }}">
            {!! Form::label('programacoes_locais[oracao]', 'Programações de Reuniões de Oração e Vigílias realizadas pelas UMPs Locai') !!}
            {!! Form::number('programacoes_locais[oracao]', isset($formulario) ? null : 0, ['class' => 'form-control', 'required' => 'required']) !!}
            <small class="text-danger">{{ $errors->first('programacoes_locais[oracao]') }}</small>
        </div>
    </div>
</div>