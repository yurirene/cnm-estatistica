
<div class="row">
    <div class="col-xl-12 mb-5 mb-xl-0">
        <button class="btn btn-primary" onclick="validarNovaDiretoria()">
            <i class="fas fa-plus"></i>
            Nova Diretoria
        </button>
    </div>
</div>
<div class="row">
    @foreach ($diretoria['membros'] as $chave => $membro)
        @include('dashboard.diretoria.card-diretoria', [
            'membro' => $membro,
            'chave' => $chave
        ])
    @endforeach
</div>
<div
    class="modal fade"
    id="modal-edicao"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modal-edicaoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-edicaoLabel">
                    Edição do cargo <span class="font-weight-bold" id="cargo_edicao"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open([
                'method' => 'POST',
                'route' => ['dashboard.diretoria.update'],
                'files' => true
            ]) !!}

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            {!! Form::label('nome', 'Nome') !!}
                            {!! Form::text('nome', null, [
                                'class' => 'form-control',
                                'autocomplete' => 'off',
                            ]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('contato', 'Contato') !!}
                            {!! Form::text('contato', null, [
                                'class' => 'form-control',
                                'autocomplete' => 'off',
                                'placeholder' => '(XX) XXXXX-XXXX, email@email.com',
                            ]) !!}
                            <small class="text-muted">
                                Os dados informados são de uso exclusivo da UMP,
                                não será disponibilizado nem acessado fora da plataforma
                                sem a sua permissão
                            </small>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <img class="img-fluid rounded" src="" id="img_modal_editar" alt="imagem" />
                        <div class="form-group">
                            {!! Form::file('imagem', [
                                'class' => 'form-control mt-2',
                            ]) !!}
                            <small>Utilize fotos quadradas (ex: 800x800)</small>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="cargo_chave" name="chave" />
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
        const rotaDiretoria = "{{ route('dashboard.diretoria.validar-ano-diretoria') }}";
        const rotaNovaDiretoria = "{{ route('dashboard.diretoria.store') }}";
        const validationToken = "{{ csrf_token() }}"

        function validarNovaDiretoria() {

            Swal.fire({
                title: 'Tem certeza?',
                text: "Essa ação deve ser feita se for o primeiro registro da diretoria ou se iniciou uma nova gestão",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return
                }
                Swal.fire({
                    title: 'Informe o ano da nova gestão',
                    input: "number",
                    inputAttributes: {
                        autocapitalize: "off"
                    },
                    showCancelButton: true,
                    confirmButtonText: "Confirmar",
                    showLoaderOnConfirm: true,
                    preConfirm: async (ano) => {
                        try {
                            $.ajax({
                                type: "POST",
                                url: rotaDiretoria,
                                data: {
                                    ano: ano,
                                    _token: validationToken
                                },
                            })
                            .done(function (response) {

                                if (!response.data) {
                                    Swal.fire({
                                        title: 'Ano inválido',
                                        text: 'Já existe uma diretoria cadastrada nesse ano',
                                        icon: 'error'
                                    });
                                    return;
                                }
                                Swal.fire({
                                    title: 'Cadastrando...'
                                });
                                Swal.showLoading()
                                $.ajax({
                                    type: "POST",
                                    url: rotaNovaDiretoria,
                                    data: {
                                        ano: ano,
                                        _token: validationToken
                                    },
                                    success: (response) => {
                                        Swal.hideLoading();
                                        Swal.fire({
                                            title: 'Diretoria cadastrada com sucesso!',
                                            icon: 'success'
                                        });
                                        setTimeout(() => {location.reload();}, 1000)
                                    },
                                    error: (error) => {
                                        Swal.hideLoading();
                                        Swal.fire({
                                            title: 'Erro',
                                            text: 'Algo deu Errado',
                                            icon: 'error'
                                        });
                                    }
                                })
                            });
                        } catch (error) {
                            console.log(error);
                        }
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                })
            })



        }

        $('#modal-edicao').on('show.bs.modal', function(event) {
            let button = $(event.relatedTarget)
            let nome = button.data('nome')
            let contato = button.data('contato')
            let path = button.data('path')
            let cargo = button.data('cargo')
            let chave = button.data('chave')

            $('#img_modal_editar').attr('src', `/${path}`);
            $('#contato').val(contato);
            $('#nome').val(nome);
            $('#cargo_edicao').text(cargo);
            $('#cargo_chave').val(chave);


        })
    </script>
@endpush
