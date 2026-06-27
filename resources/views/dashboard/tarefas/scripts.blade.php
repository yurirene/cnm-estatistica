<script>
    const ROTA_STORE_TAREFA = "{{ route('dashboard.tarefas.store') }}";
    const ROTA_UPDATE_TAREFA = "{{ route('dashboard.tarefas.update', ':id') }}";
    const $modalTarefa = $('#modal-tarefa');

    $modalTarefa.on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const acao = button.data('acao') || 'criar';
        const form = $('#form-tarefa');

        $modalTarefa.data('acao', acao);
        form.attr('action', ROTA_STORE_TAREFA);
        form.find('input[name="_method"]').remove();
        $('#modal-tarefa-label').text(acao === 'editar' ? 'Editar tarefa' : 'Nova tarefa');

        if (acao === 'editar') {
            form.attr('action', ROTA_UPDATE_TAREFA.replace(':id', button.data('id')));
            form.append('<input type="hidden" name="_method" value="PUT">');

            form.find('[name="titulo"]').val(button.data('titulo'));
            form.find('[name="descricao"]').val(button.data('descricao'));
            form.find('[name="prazo_final"]').val(button.data('prazo-final'));
            form.find('[name="periodo_notificacao"]').val(button.data('periodo'));
            form.find('[name="status"]').val(button.data('status'));
        } else {
            form[0].reset();
            form.find('[name="periodo_notificacao"]').val('semanal');
            form.find('[name="status"]').val('pendente');
        }
    });
</script>
