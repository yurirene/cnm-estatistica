<div class="tab-pane fade show active" id="primeiro" role="tabpanel" aria-labelledby="primeiro-tab">
    <div class="row">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Configure seu Site -
                                <a target="_blank" href="{{$site->url}}">{{$site->url}}</a>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($site->configuracoes['editaveis'] as $k => $item)
                        @foreach ($item as $nome => $campo)
                            @include('dashboard.apps.sites.campos', [
                                'campo' => $campo,
                                'nome' => $nome,
                                'mapeamento' => $modelo['mapeamento'],
                                'sinodal_id' => $sinodal_id,
                                'k' => $k
                            ])
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>

$(document).ready(function() {
    const ROUTE = "{{ route('dashboard.apps.sites.atualizar-config', ['sinodal_id' => $sinodal_id]) }}"
    const TOKEN = "{{ csrf_token() }}"
    var SaveButton = function (context) {
        var ui = $.summernote.ui;

        // create button
        var button = ui.button({
            contents: '<i class="fa fa-save"/> Atualizar',
            click: function () {
                var editor = $('.isSummernoteCampo');
                var valor = editor.summernote('code');
                atualizarConfig('sobreNos',editor.data('chave'),valor);

            }
        });

        return button.render();   // return button as jquery object
    }
    $(".isSummernoteCampo").summernote({
        lang: 'pt-BR',
        height: 220,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['mybutton', ['salvar']]
        ],

        buttons: {
            salvar: SaveButton
        }
    });

    $('.update').on('click', function() {
        var config = $(this).data('id');
        var chave = $(this).data('chave');
        var valor = $('#' + config).val();
        atualizarConfig(config, chave, valor);
    });



    $('.update-cargo').on('click', function() {
        var config = $(this).data('id');
        var chave = $(this).data('chave');
        var cargo = $(this).data('cargo');
        var valor = $('#' + config).val();
        console.log(config, chave, cargo, valor);
        atualizarConfig(config, chave, valor, cargo);
    });

    function atualizarConfig(config, chave, valor, cargo = null) {
        $.ajax({
            url: ROUTE,
            type: "POST",
            data: {
                _token: TOKEN,
                config: config,
                valor: valor,
                chave: chave,
                cargo: cargo
            },
            success: function(response) {

                iziToast.show({
                    title: 'Sucesso!',
                    message: response.mensagem,
                    position: 'topRight',
                });
            },
            error: function(error){

                iziToast.show({
                    title: 'Erro!',
                    message: response.mensagem,
                    position: 'topRight',
                });
            }
        });
    }
});

</script>
@endpush
