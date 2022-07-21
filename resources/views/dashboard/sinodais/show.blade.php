@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Informações da ' .$sinodal->sigla,
    'subtitulo' => $sinodal->nome
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-5 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Informações</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <a class="btn btn-primary" href="{{route('dashboard.sinodais.index')}}"><i class="fas fa-arrow-left"></i> Voltar</a>
                    
                    <div class="row mt-3">
                        <div class="col">
                            <h3><span class="badge badge-primary">Nome:</span> {{ $sinodal->nome }}</h3>
                            <h3><span class="badge badge-primary">Sínodo:</span> {{ $sinodal->sinodo }}</h3>
                            <h3><span class="badge badge-primary">Data de Organização:</span> {{ $sinodal->data_organizacao ? $sinodal->data_organizacao->format('d/m/Y') : 'Sem Informação' }}</h3>
                            <h3><span class="badge badge-primary">Redes Sociais:</span> {{ $sinodal->midias_sociais }}</h3>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                        <span>Federações Organizadas</span>
                                    </div>
                                    <div class="progress-percentage">
                                        <span>{{ $informacoes['total_federacoes_organizada'] }}%</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="{{ $informacoes['total_federacoes_organizada'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $informacoes['total_federacoes_organizada'] }}%;"></div>
                                </div>
                            </div>
                            
                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                        <span>UMPs Locais Organizadas</span>
                                    </div>
                                    <div class="progress-percentage">
                                        <span>{{ $informacoes['total_umps_organizada'] }}%</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="{{ $informacoes['total_umps_organizada'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $informacoes['total_umps_organizada'] }}%;"></div>
                                </div>
                            </div>

                            <div class="progress-wrapper">
                                <div class="progress-info">
                                    <div class="progress-label">
                                        <span>Não adotam Sociedades Internas</span>
                                    </div>
                                    <div class="progress-percentage">
                                        <span>{{ $informacoes['total_igrejas_n_sociedades'] }}%</span>
                                    </div>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="{{ $informacoes['total_igrejas_n_sociedades'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $informacoes['total_igrejas_n_sociedades'] }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-7 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Federações</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        @if(count($federacoes))
                        @foreach($federacoes as $federacao)
                        <div class="col-md-6 col-xl-4 mt-3">
                            @include('dashboard.sinodais.partes.cards', $federacao)
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_informacoes_federacoes" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modal_informacoes_federacoesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modal_informacoes_federacoesLabel">Informações de <span id="sigla_federacao"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table id="informacoes-federacao-table" class="table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center">UMP</th>
                            <th class="text-center">Nº Sócios</th>
                            <th class="text-center">Relatório</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        </div>
        </div>
    </div>
    </div>
@endsection

@push('js')
<script>
    $(function() {
        var url = '{{ route("dashboard.datatables.informacao-federacoes", ":id") }}'
        $('#modal_informacoes_federacoes').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var nome = button.data('nome')
            var modal = $(this)

            let rota = url.replace(':id', id);

            modal.find('#sigla_federacao').text(nome);

            carregarDatatable(rota);
            
        });
    });
   function carregarDatatable(url) {
        $('#informacoes-federacao-table').DataTable().destroy();
        const datatable = $('#informacoes-federacao-table').DataTable({
            dom: 'rt',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [
                {data: 'nome_ump'},
                {data: 'nro_socios'},
                {data: 'status_relatorio'},
            ]
        });
        datatable.ajax.url(url).load();
   }
</script>
@endpush