<div class="card bg-gradient-default shadow h-100">
    <div class="card-header bg-transparent">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="text-white mb-0">Avisos</h2>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(!DashboardHelper::entregouRelatorio())
        <div class="row">
            <div class="col">
                <div class="card card-stats mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto">
                                <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="card-title text-uppercase text-muted mb-0">Formulários Estatísticos</h4>
                                <span class="h5 font-weight-bold mb-0">Não deixe para a última hora</span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @canAtLeast(['dashboard.federacoes.index'])
        <div class="row mt-3">
            <div class="col">
                <div class="card card-stats mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto">
                                <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="card-title text-uppercase text-muted mb-0">Atenção</h4>
                                <span class="h5 font-weight-bold mb-0">Anexe seu comprovante de ACI</span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endCanAtLeast
        @foreach(DashboardHelper::getAvisosUsuario() as $aviso)
        <div class="row mt-3">
            <div class="col">
                <div class="card card-stats mb-4 mb-xl-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto">
                                <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="card-title text-uppercase text-muted mb-0">{{$aviso['titulo']}}</h4>
                                {!! Str::limit($aviso['texto'], 50) !!}
                                <button type="button" class="btn btn-link p-0 abrir_aviso"
                                    data-dados="{{json_encode($aviso)}}"
                                >
                                    Ver mais
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
        console.log(dados);
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
