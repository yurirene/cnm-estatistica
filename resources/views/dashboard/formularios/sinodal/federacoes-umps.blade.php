<div class="row">
    <div class="col-md-3 col-sm-6 mt-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('estrutura[ump_organizada]') ? ' has-error' : '' }}">
        {!! Form::label('estrutura[ump_organizada]', 'Quantidade de UMPs organizadas na Confederação Sinodal') !!}
        {!! Form::number('estrutura[ump_organizada]',isset($formulario) ? null : $estrutura_sinodal['quantidade_ump'], ['readonly' => true, 'id' => 'estrutura[ump_organizada]', 'class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estrutura[ump_organizada]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('estrutura[ump_nao_organizada]') ? ' has-error' : '' }}">
        {!! Form::label('estrutura[ump_nao_organizada]', 'Quantidade de igrejas sem UMPs organizadas') !!}
        {!! Form::number('estrutura[ump_nao_organizada]', isset($formulario) ? null : $estrutura_sinodal['quantidade_sem_ump'], ['readonly' => true,'class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estrutura[ump_nao_organizada]') }}</small>
        </div>  
    </div>
    <div class="col-md-3 col-sm-6 mt-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('estrutura[federacao_organizada]') ? ' has-error' : '' }}">
        {!! Form::label('estrutura[federacao_organizada]', 'Quantidade de Federações organizadas na Confederação Sinodal') !!}
        {!! Form::number('estrutura[federacao_organizada]',isset($formulario) ? null : $estrutura_sinodal['quantidade_federacoes'], ['readonly' => true, 'id' => 'estrutura[federacao_organizada]', 'class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estrutura[federacao_organizada]') }}</small>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mt-3 d-flex flex-column justify-content-end form-group">
        <div class="form-group{{ $errors->has('estrutura[federacao_nao_organizada]') ? ' has-error' : '' }}">
        {!! Form::label('estrutura[federacao_nao_organizada]', 'Quantidade de Presbitérios sem Federações organizadas') !!}
        {!! Form::number('estrutura[federacao_nao_organizada]', isset($formulario) ? null : $estrutura_sinodal['quantidade_sem_federacao'], ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estrutura[federacao_nao_organizada]') }}</small>
        </div>  
    </div>
</div>