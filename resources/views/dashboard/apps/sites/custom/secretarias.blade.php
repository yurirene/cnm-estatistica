<div class="row mb-3 mt-3">
    <div class="accordion" id="secretariaButton">
        <div class="card">
            <div class="card-header p-0" id="secretaria-unico">
                <button
                    style="font-size:1rem; color: #525f7f; font-weight: 400; text-decoration:none;"
                    class="btn btn-link btn-block text-left"
                    type="button"
                    data-toggle="collapse"
                    data-target="#secretaria"
                    aria-expanded="true"
                    aria-controls="secretaria"
                >
                    Secretarias | <small>Dica: Adicione imagens de mesmo tamanho (ex: 800x800)</small>
                </button>
            </div>

            <div id="secretaria" class="collapse show active"
                aria-labelledby="secretaria-unico" data-parent="#secretaria-unico">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-primary"
                                data-toggle="modal"
                                data-target="#nova-secretaria"
                                data-chave="{{ $chave }}"
                                data-novo="N"
                            >
                                <i class="fas fa-plus"></i>
                                Secretaria
                            </button>
                        </div>
                    </div>
                    <div class="row mt-5">
                    @foreach($campos as $key => $valor)
                        <div class="col-md-4 mb-3">
                            <div class="card shadow h-100">
                                <div class="card-header bg-primary text-white p-2">
                                    {{ $valor['secretaria'] }}
                                </div>
                                <div class="card-body p-1">
                                    <img
                                        alt="foto_{{$key}}"
                                        class="img-fluid"
                                        src="/{{ !empty($valor['path']) ? $valor['path'] : 'img/team-1.jpg' }}"
                                    />
                                    <h3 class="text-center">{{ $valor['nome'] }}</h3>
                                </div>

                                <div class="card-footer d-flex justify-content-between p-1">
                                    <button
                                        data-chave="{{ $chave }}"
                                        data-chave_secretaria="{{ $key }}"
                                        data-novo="E"
                                        data-secretaria="{{$valor['secretaria']}}"
                                        data-secretario="{{$valor['nome']}}"
                                        class="btn btn-outline-warning btn-sm"
                                        data-toggle="modal" data-target="#nova-secretaria"
                                        type="button"
                                    >
                                        <em class="fas fa-pen"></em>
                                    </button>
                                    <button
                                        class="btn btn-outline-danger btn-sm delete-secretaria"
                                        data-config="{{ $chave }}"
                                        data-chave="{{ $key }}"
                                        type="button"
                                    >
                                        <em class="fas fa-trash"></em>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="modal fade" id="nova-secretaria" tabindex="-1"
    aria-labelledby="novasecretariaLabel" aria-hidden="true"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="novasecretariaLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'route' =>
                ['dashboard.apps.sites.nova-secretaria', $sinodal_id],
                'files' => true
            ]) !!}

            <div class="modal-body">
                <div class="mb-3">
                    {!! Form::label('nome_secretaria', 'Nome da Secretaria') !!}
                    {!! Form::text(
                        'nome_secretaria',
                        null,
                        ['class' => 'form-control', 'required' => 'required', 'autocomplete' => 'off']
                    ) !!}
                </div>

                <div class="mb-3">
                    {!! Form::label('nome_secretario', 'Nome da Secretario') !!}
                    {!! Form::text(
                        'nome_secretario',
                        null,
                        ['class' => 'form-control', 'required' => 'required', 'autocomplete' => 'off']
                    ) !!}
                </div>

                <label>Foto do Secretário</label>
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input"
                            id="imagem_secretario" aria-describedby="inputenviarimg"
                            name="foto"
                        >
                        <label class="custom-file-label" for="imagem_secretario">Buscar Imagem</label>
                    </div>
                </div>

            </div>
            <input type="hidden" id="novo" name="novo" />
            <input type="hidden" id="chave_config_secretaria" name="chave" />
            <input type="hidden" id="chave_secretaria" name="chave_secretaria" />
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="subtm" class="btn btn-primary">Alterar</button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>



@push('js')
<script>

    $('#alterar-foto').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var chave = button.data('chave')
        var cargo = button.data('cargo')
        var sinodalid = button.data('sinodalid')
        $('#chave_foto').val(chave);
        $('#cargo_foto').val(cargo);
    });

    $('#nova-secretaria').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var novo = button.data('novo') == 'N' ? true : false;
        var chave = button.data('chave');
        $('#chave_config_secretaria').val(chave);
        if (novo) {
            $('#novasecretariaLabel').text('Nova Secretaria');
            $('#novo').val('S');
            return;
        }
        $('#novo').val(null);

        var chave_secretaria = button.data('chave_secretaria');
        var secretaria = button.data('secretaria');
        var secretario = button.data('secretario');
        $('#chave_secretaria').val(chave_secretaria);
        $('#nome_secretaria').val(secretaria);
        $('#nome_secretario').val(secretario);

        $('#novasecretariaLabel').text('Editar Secretaria');

    });

    const ROUTE_DELETE_SECRETARIA = "{{ route('dashboard.apps.sites.remover-secretaria', [
        'sinodal_id' => $sinodal_id,
        'chave' => ':chave',
        'config' => ':config'
    ]) }}";

    $('.delete-secretaria').on('click', function () {

        var chave = $(this).data('chave');
        var config = $(this).data('config');
        var url_remover_secretaria = ROUTE_DELETE_SECRETARIA.replace(':chave', chave)
            .replace(':config', config);

        deleteRegistro(url_remover_secretaria);
    })

</script>

@endpush
