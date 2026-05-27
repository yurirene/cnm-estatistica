<script>
    const ROTA_STORE = "{{ route('dashboard.secretaria-executiva.resolucoes.store') }}";
    const ROTA_UPDATE = "{{ route('dashboard.secretaria-executiva.resolucoes.update', ':id') }}";
    const ROTA_RESPONSAVEIS = "{{ route('dashboard.secretaria-executiva.responsaveis') }}";
    const PODE_GERENCIAR = {{ $podeGerenciar ? 'true' : 'false' }};
    const $modalResolucao = $('#modal-resolucao');

    function configurarSelectResponsavel() {
        if (!PODE_GERENCIAR || !$('#responsavel_id').length) {
            return;
        }

        const $select = $('#responsavel_id');

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2({
            dropdownParent: $modalResolucao.find('.modal-content'),
            ajax: {
                url: ROTA_RESPONSAVEIS,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { term: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                }
            },
            minimumInputLength: 0,
            placeholder: 'Selecione o responsável',
            allowClear: true,
            width: '100%'
        });
    }

    function preencherResponsavelEdicao(button) {
        if (!PODE_GERENCIAR) {
            return;
        }

        const $select = $('#responsavel_id');
        const option = new Option(
            button.data('responsavel-text'),
            button.data('responsavel-id'),
            true,
            true
        );

        $select.empty().append(option).trigger('change');
    }

    $modalResolucao.on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const acao = button.data('acao') || 'criar';
        const form = $('#form-resolucao');

        $modalResolucao.data('edit-button', button);
        $modalResolucao.data('acao', acao);

        form.attr('action', ROTA_STORE);
        form.find('input[name="_method"]').remove();
        $('#modal-resolucao-label').text(acao === 'editar' ? 'Editar resolução' : 'Nova resolução');

        if (acao === 'editar') {
            const id = button.data('id');
            form.attr('action', ROTA_UPDATE.replace(':id', id));
            form.append('<input type="hidden" name="_method" value="PUT">');

            form.find('[name="titulo"]').val(button.data('titulo'));
            form.find('[name="descricao"]').val(button.data('descricao'));
            form.find('[name="origem"]').val(button.data('origem'));
            form.find('[name="status"]').val(button.data('status'));
            form.find('[name="prioridade"]').val(button.data('prioridade'));
            form.find('[name="data_aprovacao"]').val(button.data('data-aprovacao'));
            form.find('[name="prazo_final"]').val(button.data('prazo-final'));
        } else {
            form[0].reset();
        }
    });

    $modalResolucao.on('shown.bs.modal', function () {
        // Evita que o Bootstrap feche o modal ao focar no Select2
        $(document).off('focusin.bs.modal');

        configurarSelectResponsavel();

        const acao = $modalResolucao.data('acao');
        const button = $modalResolucao.data('edit-button');

        if (acao === 'editar') {
            preencherResponsavelEdicao(button);
        } else if (PODE_GERENCIAR) {
            $('#responsavel_id').val(null).trigger('change');
        }
    });

    $modalResolucao.on('hidden.bs.modal', function () {
        if ($('#responsavel_id').hasClass('select2-hidden-accessible')) {
            $('#responsavel_id').select2('destroy');
        }
    });
</script>
