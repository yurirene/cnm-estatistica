@php
    $game = $game ?? DashboardHelper::getGamificacao();
@endphp
<div class="card avisos-card shadow h-100">
    <div class="card-header bg-transparent">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="text-white mb-0">Avisos</h2>
            </div>
        </div>
    </div>
    <div class="card-body avisos-lista">
        @if($game)
            @foreach($game->avisosPontos as $avisoGame)
                <div class="aviso-item {{ $avisoGame['tipo'] === 'aviso' ? 'warn' : '' }}">
                    <div class="aviso-dot">
                        <i class="fas {{ $avisoGame['tipo'] === 'aviso' ? 'fa-info' : 'fa-exclamation' }}"></i>
                    </div>
                    <div>
                        <b style="display:block;font-size:12.5px;margin-bottom:2px">{{ $avisoGame['titulo'] }}</b>
                        <span style="font-size:11.5px;color:#B7C1E0;line-height:1.4">{{ $avisoGame['texto'] }}</span>
                        <span style="font-size:10.5px;color:#FFD8A8;font-weight:700;margin-top:4px;display:block">
                            {{ $avisoGame['pts'] }} pts em risco
                        </span>
                    </div>
                </div>
            @endforeach
        @endif
        @if(!DashboardHelper::entregouRelatorio())
        <div class="aviso-item">
            <div class="aviso-dot"><i class="fas fa-exclamation"></i></div>
            <div>
                <b style="display:block;font-size:12.5px;margin-bottom:2px">Formulários Estatísticos</b>
                <span style="font-size:11.5px;color:#B7C1E0">Não deixe para a última hora</span>
            </div>
        </div>
        @endif
        @can('rota-permitida', ['dashboard.federacoes.index'])
        @if(!DashboardHelper::entregouComprovante())
        <div class="aviso-item">
            <div class="aviso-dot"><i class="fas fa-exclamation"></i></div>
            <div>
                <b style="display:block;font-size:12.5px;margin-bottom:2px">Anexe seu comprovante de ACI</b>
                <span style="font-size:11.5px;color:#B7C1E0">Necessário para pontuar no pilar Anuidade.</span>
            </div>
        </div>
        @endif
        @endCan
        @foreach(DashboardHelper::getAvisosUsuario() as $aviso)
        <div class="aviso-item warn">
            <div class="aviso-dot"><i class="fas fa-bullhorn"></i></div>
            <div>
                <b style="display:block;font-size:12.5px;margin-bottom:2px">{{ $aviso['titulo'] }}</b>
                <span style="font-size:11.5px;color:#B7C1E0">{!! Str::limit($aviso['texto'], 50) !!}</span>
                <button type="button" class="btn btn-link p-0 abrir_aviso text-white"
                    data-dados="{{ json_encode($aviso) }}"
                >
                    Ver mais
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>


@php
    $aviso = DashboardHelper::getAvisosUsuarioModal();
@endphp
@if(!empty($aviso))
<div class="modal fade" id="modal-aviso" tabindex="-1" role="dialog" aria-labelledby="modal-aviso" aria-hidden="true">
    <div class="modal-dialog modal-warning modal-dialog-centered modal-" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal-title-aviso">Atenção</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="py-3 text-center">
                    <i class="fas fa-3x fa-bullhorn text-danger shadow"></i>
                    <h3 class="text-gradient text-white mt-4 mb-3">{{ $aviso['titulo'] }}</h3>
                    {!! $aviso['texto'] !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                    class="btn btn-secondary ml-auto ciente"
                    data-dismiss="modal"
                    data-id="{{ $aviso['id'] }}"
                >
                    Ciente!
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="modal-aviso-show"
    tabindex="-1" role="dialog"
    aria-labelledby="modal-aviso" aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal-title-aviso">Atenção</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="py-3 text-center">
                    <i class="fas fa-3x fa-bullhorn text-danger shadow"></i>
                    <h3 class="text-gradient text-white mt-4 mb-3" id="titulo_modal_show"></h3>
                    <div id="corpo_modal_show"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary ml-auto" type="button" data-bs-dismiss="modal" aria-label="Close">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>
@push('js')
<script>
    $('.ciente').on('click', function() {
        var id = $(this).data('id')
        var route = "{{ route('dashboard.avisos.visualizado', ':id') }}";
        route  = route.replace(':id', id);
        $.ajax({
            url: route
        });
    })

    $('.abrir_aviso').on('click', function(e) {
        let botao = $(e.currentTarget)
        let dados = botao.data('dados');
        $('#corpo_modal_show').html(dados.texto);
        $('#titulo_modal_show').text(dados.titulo);
        $('#modal-aviso-show').modal('show');
    })

</script>
@if(!empty($aviso))
    <script>
        $('#modal-aviso').modal('show');
    </script>
@endif
@endpush
