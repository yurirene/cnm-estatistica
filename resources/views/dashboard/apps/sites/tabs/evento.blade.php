<div
    class="tab-pane fade {{ session()->has('aba') && session('aba') == 'evento' ? 'show active' : ''}}"
    id="segundo" role="tabpanel" aria-labelledby="segundo-tab"
>
    <div class="row">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Configure seu Evento -
                                {{-- <a target="_blank" href="{{$site->url}}">{{$site->url}}</a> --}}
                            </h3>
                            TODO:
                            Botao de Ativação do Evento<br>
                            Botão para resetar configs <br>
                            Tab inscritos com limpeza de inscritos <br>
                            Campo obrigatório (opcional)<br>
                            Modal de Confirmação de inscrição

                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::model(
                        $evento,
                        [
                            'route' => ['dashboard.apps.sites.atualizar-config-evento', $evento->id],
                            'method' => 'PUT',
                            'files' => true
                        ]
                    ) !!}
                    <div class="mb-3">
                        {!! Form::label('nome', 'Nome do Evento') !!}
                        {!! Form::text('nome', null, ['class' => 'form-control', 'required' => 'required']) !!}
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            {!! Form::label('data_inicio', 'Data Inicial') !!}
                            {!! Form::text(
                                'data_inicio',
                                null,
                                ['class' => 'form-control isDate', 'required' => 'required', 'autocomplete' => 'off']
                            ) !!}
                        </div>

                        <div class="col-md-6">
                            {!! Form::label('data_fim', 'Data Final') !!}
                            <small class="ml-3 text-muted">(Se houver mais de um dia)</small>
                            {!! Form::text(
                                'data_fim',
                                null,
                                ['class' => 'form-control isDate', 'autocomplete' => 'off']
                            ) !!}
                        </div>
                    </div>

                    <div class="mb-3">
                        {!! Form::label('descricao', 'Descrição') !!}
                        {!! Form::textarea(
                            'descricao',
                            null,
                            ['class' => 'form-control isSummernoteCampoEvento', 'required' => 'required']
                        ) !!}
                    </div>

                    <div class="mb-3">
                        <label>Banner da Página do Evento</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input"
                                id="image-a" aria-describedby="inputenviarimga"
                                name="arte_evento_principal"
                            >
                            <label class="custom-file-label" for="image">Buscar Imagem</label>
                            <small class="text-muted">1140 x 300px</small>
                        </div>
                        @if(isset($evento->path_arte_1))
                        <img src="/{{$evento->path_arte_1}}" class="img-thumbnail" alt="banner">
                        @endif
                    </div>

                    <h4>Formulário de Inscrição</h4>
                    <button type="button" class="btn btn-sm btn-primary mb-3" id="add_campo">Adicionar Campo</button>

                    <div id="forms"></div>

                    <div class="row mt-3">
                        <div class="col">
                            <button class="btn btn-success" type="submit">
                                <i class="fas fa-save"></i>
                                Salvar
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>


function makeForm(id, type, value, option = '') {
    return `<div class="row mb-3">
        <div class="col-md-4">
            <input class="form-control campo_form" name="form[${id}][campo]"
                type="text" placeholder="Nome do campo"
                value="${value}"
            >
        </div>
        <div class="col-md-4">
            <select class="form-control campo_form_type" name="form[${id}][tipo]">
                <option value="text">Texto</option>
                <option value="data">Data</option>
                <option value="select">Seleção</option>
                <option value="remover">Remover</option>
            </select>
        </div>

        <div class="col-md-4">
            <input class="form-control campo_form_option" style="display:none" name="form[${id}][option]"
                type="text" placeholder="Separe as opções por vírgula"
                value="${option}"
            >
            <small class="text-muted campo_form_option" style="display:none">
                Separe as opções por vírgula
            </small>
        </div>
    </div>`;
}

$(document).ready(function() {
    const ROUTE = "{{ route('dashboard.apps.sites.atualizar-config-evento', ['evento_id' => $evento->id]) }}"
    const TOKEN = "{{ csrf_token() }}"

    $(".isSummernoteCampoEvento").summernote({
        lang: 'pt-BR',
        height: 220,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'table']],
        ]
    });

    const FORMS = @json($evento->form);
    FORMS.forEach(function(item, indice) {
        $('#forms').append(makeForm(indice, item.tipo, item.campo, item.option));
        $('select[name="form['+indice+'][tipo]"] ').val(item.tipo);
    });
    refreshChange();
});

$('#add_campo').on('click', function() {
    let total = $('.campo_form').length + 1;
    $('#forms').append(makeForm(total, 'text', ''));
    refreshChange();
});

function refreshChange() {
    $('.campo_form_type').on('change', function() {
        let tipo = $(this).val();
        let option = $(this).closest('.row').find('.campo_form_option');
        if (tipo == 'select') {
            option.show();
        } else {
            option.hide();
        }
    })
    $('.campo_form_type').trigger('change');
}

</script>
@endpush
