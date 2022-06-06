<div class="row">
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('federacao_ump[ump_organizada]') ? ' has-error' : '' }}">
        {!! Form::label('federacao_ump[ump_organizada]', 'Quantidade de UMPs organizadas na Confederação Sinodal') !!}
        {!! Form::number('federacao_ump[ump_organizada]',0, ['id' => 'federacao_ump[ump_organizada]', 'class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('federacao_ump[ump_organizada]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('federacao_ump[ump_nao_organizada]') ? ' has-error' : '' }}">
        {!! Form::label('federacao_ump[ump_nao_organizada]', 'Quantidade de igrejas sem UMPs organizadas') !!}
        {!! Form::number('federacao_ump[ump_nao_organizada]', 0, ['class' => 'form-control isMoney', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('federacao_ump[ump_nao_organizada]') }}</small>
        </div>  
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('federacao_ump[federacao_organizada]') ? ' has-error' : '' }}">
        {!! Form::label('federacao_ump[federacao_organizada]', 'Quantidade de Federações organizadas na Confederação Sinodal') !!}
        {!! Form::number('federacao_ump[federacao_organizada]',0, ['id' => 'federacao_ump[federacao_organizada]', 'class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('federacao_ump[federacao_organizada]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3">
        <div class="form-group{{ $errors->has('federacao_ump[federacao_nao_organizada]') ? ' has-error' : '' }}">
        {!! Form::label('federacao_ump[federacao_nao_organizada]', 'Quantidade de Presbitérios sem Federações organizadas') !!}
        {!! Form::number('federacao_ump[federacao_nao_organizada]', 0, ['class' => 'form-control isMoney', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('federacao_ump[federacao_nao_organizada]') }}</small>
        </div>  
    </div>
</div>