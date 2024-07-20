<div class="row mb-3 mt-3">
    <div class="accordion" id="diretoriaButton">
        <div class="card">
            <div class="card-header p-0" id="diretoria-unico">
                <button
                    style="font-size:1rem; color: #525f7f; font-weight: 400; text-decoration:none;"
                    class="btn btn-link btn-block text-left"
                    type="button"
                    data-toggle="collapse"
                    data-target="#diretoria"
                    aria-expanded="true"
                    aria-controls="diretoria"
                >
                    Diretoria | <small>Dica: Adicione imagens de mesmo tamanho (ex: 800x800)</small>
                </button>
            </div>

            <div id="diretoria" class="collapse show active" aria-labelledby="diretoria-unico" data-parent="#diretoria-unico">
                <div class="card-body">
                    <div class="row mt-3">
                    @foreach($campos as $key => $valor)
                        <div class="col-md-4 mb-3">
                            <div class="card shadow">
                                <div class="card-header bg-primary text-white p-2">
                                    {{ $valor['titulo'] }}
                                </div>
                                <div class="card-body p-1">
                                    <img class="img-fluid" src="/{{ !empty($valor['path']) ? $valor['path'] : 'img/team-1.jpg' }}" />
                                </div>

                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="d-grid gap-2">
                                                <button
                                                    data-chave="{{ $chave }}"
                                                    data-cargo="{{ $key }}"
                                                    class="btn btn-outline-warning"
                                                    data-toggle="modal" data-target="#alterar-foto"
                                                    type="button"
                                                >
                                                    <em class="fas fa-camera"></em>
                                                    Alterar Foto
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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

<div class="modal fade" id="alterar-foto" tabindex="-1" aria-labelledby="alterar-fotoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alterar-fotoLabel">Alterar Foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'route' =>
                ['dashboard.apps.sites.atualizar-foto-diretoria', $sinodal_id],
                'files' => true
            ]) !!}

            <div class="modal-body">
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input"
                            id="image" aria-describedby="inputenviarimg"
                            name="foto"
                        >
                        <label class="custom-file-label" for="image">Buscar Imagem</label>
                    </div>
                </div>
            </div>
            <input type="hidden" id="chave_foto" name="chave" />
            <input type="hidden" id="cargo_foto" name="cargo" />
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

</script>

@endpush
