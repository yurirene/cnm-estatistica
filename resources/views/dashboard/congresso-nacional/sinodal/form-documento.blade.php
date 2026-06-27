{!! Form::open([
        'method' => 'POST',
        'route' => 'dashboard.cn.sinodal.documento.store',
        'files' => true
    ]) !!}
<div class="row">
    <div class="col-md-12">
        <h5 class="mt-3">Novo Documento</h5>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('titulo', 'Título') !!}
            {!! Form::text(
                'titulo',
                null,
                [
                    'class' => 'form-control',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => 'Digite o título do documento'
                ]
            ) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('arquivo', 'Documento (PDF)') !!}
            {!! Form::file(
                'arquivo',
                [
                    'required' => 'required',
                    'class' => 'form-control',
                    'accept' => 'application/pdf'
                ]
            ) !!}
            <small class="form-text text-muted">Apenas arquivos PDF (máx. 1MB)</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label>&nbsp;</label>
            <div>
                <button class="btn btn-success">Enviar Documento</button>
            </div>
        </div>
    </div>
</div>
{!! Form::close() !!}

