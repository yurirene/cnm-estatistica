<div class="modal fade" id="modal-importar" tabindex="-1" aria-labelledby="modal-importar-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-importar-label">Importar resoluções (CSV)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open([
                'method' => 'POST',
                'route' => ['dashboard.secretaria-executiva.resolucoes.importar'],
                'files' => true,
            ]) !!}
            <div class="modal-body">
                <p class="text-muted">
                    Utilize o modelo CSV com separador <strong>;</strong> ou <strong>,</strong>.
                    Campos: titulo, descricao, origem, status, prioridade, data_aprovacao, prazo_final, responsavel_email (opcional).
                </p>
                <a href="{{ route('dashboard.secretaria-executiva.resolucoes.modelo-importacao') }}" class="btn btn-sm btn-outline-secondary mb-3">
                    <i class="fas fa-download"></i> Baixar modelo
                </a>
                <div class="form-group">
                    {!! Form::label('arquivo', 'Arquivo CSV') !!}<small class="text-danger">*</small>
                    {!! Form::file('arquivo', ['class' => 'form-control', 'required' => true, 'accept' => '.csv,.txt']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-import"></i> Importar
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
