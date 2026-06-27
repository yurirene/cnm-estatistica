@if(!empty($reuniao) && $reuniao['status'] == 1)
    {!! Form::open([
            'method' => 'POST',
            'route' => 'dashboard.ce-sinodal.enviar-documento',
            'files' => true
        ]) !!}
    <div class="row">
        <div class="col-md-12">
            
            @if(isset($documentosAutomaticos['diretoria']))
            <div>
                <strong>Diretoria:</strong>
                <span class="badge badge-{{ $documentosAutomaticos['diretoria'] ? 'success' : 'danger' }}">{{ $documentosAutomaticos['diretoria'] ? 'Entregue' : 'Pendente' }}</span>
            </div>    
            @endif
            @if(isset($documentosAutomaticos['relatorio_estatistico']))
            <div>
                <strong>Relatório Estatístico:</strong>
                <span class="badge badge-{{ $documentosAutomaticos['relatorio_estatistico'] ? 'success' : 'danger' }}">{{ $documentosAutomaticos['relatorio_estatistico'] ? 'Entregue' : 'Pendente' }}</span>
            </div>
            @endif
        </div>
    </div>
    <h4 class="mt-3">Novo Documento</h4>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('titulo', 'Título', ['id' => 'titulo_titulo']) !!}
                {!! Form::text(
            'titulo',
            null,
            [
                'class' => 'form-control',
                'required' => true,
                'autocomplete' => 'off',
            ]
        ) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('arquivo', 'Documento', ['id' => 'titulo_doc']) !!}
                {!! Form::file(
            'arquivo',
            ['required' => 'required', 'class' => 'form-control',]
        ) !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <button class="btn btn-success">Enviar</button>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@endif
