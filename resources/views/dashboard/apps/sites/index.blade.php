@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Sites'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Configure seu Site</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($modelo['configuracoes']['editaveis'] as $item)
                        @foreach ($item as $nome => $campo)
                            @include('dashboard.apps.sites.campos', [
                                'campo' => $campo,
                                'nome' => $nome,
                                'mapeamento' => $modelo['mapeamento'],
                                'sinodal_id' => 'b3201496-329d-43b4-82ba-8ead42f25b1f'
                            ])
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>

$(document).ready(function() {
    var SaveButton = function (context) {
        var ui = $.summernote.ui;

        // create button
        var button = ui.button({
            contents: '<i class="fa fa-save"/> Atualizar',
            click: function () {
                var markup = $('.isSummernoteCampo').summernote('code');
                console.log(markup);
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
});

</script>
@endpush
