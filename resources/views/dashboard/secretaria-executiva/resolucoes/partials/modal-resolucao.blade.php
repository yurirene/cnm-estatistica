<div class="modal fade" id="modal-resolucao" tabindex="-1" aria-labelledby="modal-resolucao-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-resolucao-label">Resolução</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open([
                'id' => 'form-resolucao',
                'method' => 'POST',
                'route' => ['dashboard.secretaria-executiva.resolucoes.store'],
                'files' => true,
            ]) !!}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            {!! Form::label('titulo', 'Título') !!}<small class="text-danger">*</small>
                            {!! Form::text('titulo', null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('origem', 'Origem') !!}<small class="text-danger">*</small>
                            {!! Form::select('origem', $origens, null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('descricao', 'Descrição') !!}<small class="text-danger">*</small>
                    {!! Form::textarea('descricao', null, ['class' => 'form-control', 'rows' => 3, 'required' => true]) !!}
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('status', 'Status') !!}
                            {!! Form::select('status', $status, null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('prioridade', 'Prioridade') !!}<small class="text-danger">*</small>
                            {!! Form::select('prioridade', $prioridades, null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('data_aprovacao', 'Data de aprovação') !!}<small class="text-danger">*</small>
                            {!! Form::date('data_aprovacao', null, ['class' => 'form-control', 'required' => true]) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('prazo_final', 'Prazo final') !!}
                            {!! Form::date('prazo_final', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    @if($podeGerenciar)
                    <div class="col-md-8">
                        <div class="form-group">
                            {!! Form::label('responsavel_id', 'Responsável') !!}
                            <small class="text-muted">(opcional — pode definir depois)</small>
                            {!! Form::select('responsavel_id', [], null, [
                                'class' => 'form-control isSelect2Modal',
                                'id' => 'responsavel_id',
                                'style' => 'width:100%;',
                            ]) !!}
                        </div>
                    </div>
                    @endif
                </div>
                <div class="form-group">
                    {!! Form::label('anexos[]', 'Anexos') !!}
                    {!! Form::file('anexos[]', ['class' => 'form-control', 'multiple' => true, 'accept' => '.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
