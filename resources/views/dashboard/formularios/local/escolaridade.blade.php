<div class="row">
    <div class="col-md-3  d-flex flex-column justify-content-end">
        <div class="form-group{{ $errors->has('escolaridade[fundamental]') ? ' has-error' : '' }}">
        {!! Form::label('escolaridade[fundamental]', 'Sócios com Ensino Fundamental concluído') !!}
        {!! Form::number(
            'escolaridade[fundamental]',
            isset($formulario) ? null : 0,
            [
                'class' => 'form-control',
                'required' => 'required',
                'data-toggle' => "tooltip",
                'data-placement' => "top",
                'title' => "Sócios que ainda estão cursando o ensino médio ou não fizeram o ensino médio."
            ]
        ) !!}
        @if (!empty($coletorDados))
            <small class="text-muted">Informação do coletor de dados: {{ $coletorDados['escolaridade']['fundamental'] }}</small>
        @endif
        <small class="text-danger">{{ $errors->first('escolaridade[fundamental]') }}</small>
        </div>
    </div>
    <div class="col-md-3  d-flex flex-column justify-content-end">
        <div class="form-group{{ $errors->has('escolaridade[medio]') ? ' has-error' : '' }}">
        {!! Form::label('escolaridade[medio]', 'Sócios com Ensino Médio concluído') !!}
        {!! Form::number(
            'escolaridade[medio]',
            isset($formulario) ? null : 0,
            [
                'class' => 'form-control',
                'required' => 'required',
                'data-toggle' => "tooltip",
                'data-placement' => "top",
                'title' => "Sócios que concluíram o ensino médio ou sócios que ainda não concluíram o ensino superior."
            ]
        ) !!}
        @if (!empty($coletorDados))
            <small class="text-muted">Informação do coletor de dados: {{ $coletorDados['escolaridade']['medio'] }}</small>
        @endif
        <small class="text-danger">{{ $errors->first('escolaridade[medio]') }}</small>
        </div>
    </div>
    <div class="col-md-3  d-flex flex-column justify-content-end">
        <div class="form-group{{ $errors->has('escolaridade[tecnico]') ? ' has-error' : '' }}">
        {!! Form::label('escolaridade[tecnico]', 'Sócios com Ensino Técnico concluído') !!}
        {!! Form::number(
            'escolaridade[tecnico]',
            isset($formulario) ? null : 0,
            [
                'class' => 'form-control',
                'required' => 'required',
                'data-toggle' => "tooltip",
                'data-placement' => "top",
                'title' => "Sócios que concluíram o ensino médio técnico mas não concluíram o ensino superior. Se concluiu o ensino superior não adicione aqui"
            ]
        ) !!}
        @if (!empty($coletorDados))
            <small class="text-muted">Informação do coletor de dados: {{ $coletorDados['escolaridade']['tecnico'] }}</small>
        @endif
        <small class="text-danger">{{ $errors->first('escolaridade[tecnico]') }}</small>
        </div>
    </div>
    <div class="col-md-3  d-flex flex-column justify-content-end">
        <div class="form-group{{ $errors->has('escolaridade[superior]') ? ' has-error' : '' }}">
        {!! Form::label('escolaridade[superior]', 'Sócios com Ensino Superior concluído') !!}
        {!! Form::number(
            'escolaridade[superior]',
            isset($formulario) ? null : 0,
            [
                'class' => 'form-control',
                'required' => 'required',
                'data-toggle' => "tooltip",
                'data-placement' => "top",
                'title' => "Sócios que concluíram o ensino superior ou ainda não concluíram a Pós-Graduação"
            ]
        ) !!}
        @if (!empty($coletorDados))
            <small class="text-muted">Informação do coletor de dados: {{ $coletorDados['escolaridade']['superior'] }}</small>
        @endif
        <small class="text-danger">{{ $errors->first('escolaridade[superior]') }}</small>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3  d-flex flex-column justify-content-end">
        <div class="form-group{{ $errors->has('escolaridade[pos]') ? ' has-error' : '' }}">
        {!! Form::label('escolaridade[pos]', 'Sócios com a Pós-Graduação concluída') !!}
        {!! Form::number(
            'escolaridade[pos]',
            isset($formulario) ? null : 0,
            [
                'class' => 'form-control',
                'required' => 'required',
                'data-toggle' => "tooltip",
                'data-placement' => "top",
                'title' => "Sócios que concluíram ao menos uma pós-graduação, se ainda está cursando contabilize em ensino superior"
            ]
        ) !!}
        @if (!empty($coletorDados))
            <small class="text-muted">Informação do coletor de dados: {{ $coletorDados['escolaridade']['pos'] }}</small>
        @endif
        <small class="text-danger">{{ $errors->first('escolaridade[pos]') }}</small>
        </div>
    </div>
</div>
