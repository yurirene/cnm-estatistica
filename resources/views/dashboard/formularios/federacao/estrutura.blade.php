<div class="row">
    <div class="col-md-3 d-flex flex-column justify-content-end ">
        <div class="form-group{{ $errors->has('estrutura[ump_organizada]') ? ' has-error' : '' }}">
        {!! Form::label('estrutura[ump_organizada]', 'Quantidade de UMPs organizadas na Federação:') !!} 
        {!! Form::number('estrutura[ump_organizada]', $estrutura_federacao['quantidade_umps'], ['class' => 'form-control', 'required' => 'required', 'readonly' => true]) !!}
        <small class="text-danger">{{ $errors->first('estrutura[ump_organizada]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end ">
        <div class="form-group{{ $errors->has('estrutura[ump_nao_organizada]') ? ' has-error' : '' }}">
        {!! Form::label('estrutura[ump_nao_organizada]', 'Quantidade de igrejas do Presbitério sem UMPs organizadas:') !!}
        {!! Form::number('estrutura[ump_nao_organizada]', $estrutura_federacao['quantidade_sem_ump'], ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estrutura[ump_nao_organizada]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end ">
        <div class="form-group{{ $errors->has('estrutura[nro_repasse]') ? ' has-error' : '' }}">
        {!! Form::label('estrutura[nro_repasse]', 'Quantidade de UMPs que fizeram o repasse da ACI para a Federação:') !!} 
        {!! Form::number('estrutura[nro_repasse]', $estrutura_federacao['nro_repasse'], ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estrutura[nro_repasse]') }}</small>
        </div>
    </div>
    <div class="col-md-3 d-flex flex-column justify-content-end ">
        <div class="form-group{{ $errors->has('estrutura[nro_sem_repasse]') ? ' has-error' : '' }}">
        {!! Form::label('estrutura[nro_sem_repasse]', 'Quantidade de UMPs que NÃO fizeram o repasse da ACI para a Federação:') !!} 
        {!! Form::number('estrutura[nro_sem_repasse]', $estrutura_federacao['nro_sem_repasse'], ['class' => 'form-control', 'required' => 'required']) !!}
        <small class="text-danger">{{ $errors->first('estrutura[nro_sem_repasse]') }}</small>
        </div>
    </div>
</div>