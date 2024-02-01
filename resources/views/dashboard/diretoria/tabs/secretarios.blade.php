
<div class="row">
    <div class="col-xl-12 mb-5 mb-xl-0">
        <button
            class="btn btn-primary"
            data-target="#modal-edicao-secretario"
            data-toggle="modal"
            data-novo="sim"
        >
            <i class="fas fa-plus"></i>
            Nova Secretaria
        </button>
    </div>
</div>
<div class="row">
    @forelse ($secretarios as $secretario)
        @include('dashboard.diretoria.card-secretario', [
            'secretario' => $secretario,
        ])
    @empty
        <p>Sem secretários cadastrados<p>
    @endforelse
</div>
<div
    class="modal fade"
    id="modal-edicao-secretario"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modal-edicao-secretarioLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-edicao-secretarioLabel1">
                    Edição da Secretaria: <span class="font-weight-bold" id="cargo_edicao"></span>
                </h5>
                <h5 class="modal-title" id="modal-edicao-secretarioLabel2" style="display:none;">
                    Cadastro da Secretaria
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open([
                'method' => 'POST',
                'route' => ['dashboard.secretario.store-update'],
                'files' => true
            ]) !!}

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            {!! Form::label('secretaria', 'Secretaria de Atividade') !!}
                            {!! Form::text('secretaria', null, [
                                'class' => 'form-control',
                                'autocomplete' => 'off',
                                'placeholder' => 'Estatística, Espiritualidade ...',
                            ]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('nome_secretario', 'Nome do(a) Secretário(a)') !!}
                            {!! Form::text('nome_secretario', null, [
                                'class' => 'form-control',
                                'autocomplete' => 'off',
                            ]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('contato_secretario', 'Contato') !!}
                            {!! Form::text('contato_secretario', null, [
                                'class' => 'form-control',
                                'autocomplete' => 'off',
                                'placeholder' => '(XX) XXXXX-XXXX, email@email.com',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-5">
                        <img class="img-fluid rounded" src="" id="img_modal_editar_secretario" alt="imagem" />
                        <div class="form-group">
                            <label>Foto do(a) Secretário(a)<label>
                            {!! Form::file('imagem_secretario', [
                                'class' => 'form-control mt-2',
                            ]) !!}
                            <small>Utilize fotos quadradas (ex: 800x800)</small>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="secretario_id" name="secretario_id" />
            <input type="hidden" id="diretoria_id" name="diretoria_id" value="{{$diretoria['id']}}" />
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="subtm" class="btn btn-primary">Atualizar</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
@push('js')
    <script>
        const ROTA_REMOVE_SECRETARIO = "{{ route('dashboard.secretario.delete', ':id') }}"

        $('#modal-edicao-secretario').on('show.bs.modal', function(event) {
            let button = $(event.relatedTarget)
            let novo = button.data('novo')

            if (novo == 'sim') {
                $('#img_modal_editar_secretario').hide();
                $('#modal-edicao-secretarioLabel1').hide();
                $('#modal-edicao-secretarioLabel2').show();
                $('#secretario_id').val('')
                return;
            }

            $('#modal-edicao-secretarioLabel1').show();
            $('#modal-edicao-secretarioLabel2').hide();
            let nome = button.data('nome')
            let contato = button.data('contato')
            let path = button.data('path')
            let id = button.data('id')
            let secretaria = button.data('secretaria')

            $('#img_modal_editar_secretario').attr('src', `/${path}`);
            $('#contato_secretario').val(contato);
            $('#nome_secretario').val(nome);
            $('#secretaria').val(secretaria);
            $('#secretario_id').val(id)


        })

        $('.remover-secretario').on('click', function () {
            let button = $(this);
            let secretaria = button.data('secretaria')
            let id = button.data('id')
            let rota = ROTA_REMOVE_SECRETARIO.replace(':id', id);
            alertConfirmar(rota, `Deseja remover a Secretaria ${secretaria}?`);
        });
    </script>
@endpush
