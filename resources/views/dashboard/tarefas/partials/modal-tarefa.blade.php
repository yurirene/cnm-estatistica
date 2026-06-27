<div class="modal fade" id="modal-tarefa" tabindex="-1" aria-labelledby="modal-tarefa-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-tarefa-label">Tarefa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open([
                'id' => 'form-tarefa',
                'method' => 'POST',
                'route' => ['dashboard.tarefas.store'],
            ]) !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('titulo', 'Título') !!}<small class="text-danger">*</small>
                    {!! Form::text('titulo', null, ['class' => 'form-control', 'required' => true]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('descricao', 'Descrição') !!}
                    {!! Form::textarea('descricao', null, ['class' => 'form-control', 'rows' => 3]) !!}
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('prazo_final', 'Prazo final') !!}
                            {!! Form::date('prazo_final', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('periodo_notificacao', 'Notificar a cada') !!}<small class="text-danger">*</small>
                            {!! Form::select('periodo_notificacao', $periodos, 'semanal', [
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('status', 'Status') !!}
                            {!! Form::select('status', $status, 'pendente', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                <p class="text-muted small mb-0">
                    Você receberá lembretes no Telegram no intervalo escolhido até concluir a tarefa.
                    Configure seu Chat ID no botão Telegram acima.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
