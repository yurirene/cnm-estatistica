<div class="modal fade" id="modal-telegram" tabindex="-1" aria-labelledby="modal-telegram-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-telegram-label">Configurar Telegram</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open([
                'method' => 'PUT',
                'route' => ['dashboard.secretaria-executiva.telegram.update'],
            ]) !!}
            <div class="modal-body">
                <p class="text-muted">
                    Informe o <strong>Chat ID</strong> do Telegram para receber alertas de novas resoluções e prazos.
                    Converse com o bot da CNM e use ferramentas como @userinfobot para obter seu ID.
                </p>
                <div class="form-group">
                    {!! Form::label('telegram_chat_id', 'Chat ID do Telegram') !!}
                    {!! Form::text('telegram_chat_id', $usuario->telegram_chat_id, [
                        'class' => 'form-control',
                        'placeholder' => 'Ex: 123456789',
                        'autocomplete' => 'off',
                    ]) !!}
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
