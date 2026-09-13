@extends('layouts.app')

@section('content')
    @include('dashboard.partes.head', [
        'titulo' => 'Início',
        'url_tutorial' => config('tutoriais.index.vice')
    ])

    <div class="container-fluid exec-dashboard">
        @include('dashboard.index.presidente.cards')
        @include('dashboard.index.partes.executivo.estrutura', [
            'totalizador' => DashboardHelper::getTotalizadores()
        ])

        <div class="row">
            <div class="col-xl-4 mt-3">
                @include('dashboard.index.partes.executivo.qualidade')
            </div>
            <div class="col-xl-8 mt-3">
                @include('dashboard.index.partes.executivo.mapa')
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 mt-3">
                <div class="exec-panel">
                    <div class="exec-table-toolbar">
                        <h4 style="margin:0;">Entrega de Formulários por Sinodal</h4>
                        <div class="d-flex align-items-end" style="gap: .75rem;">
                            @include('dashboard.index.partes.filtro-regiao')
                            @include('dashboard.index.partes.filtro-ano-referencia')
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="sinodal-entregues-table" class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Sinodal</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Federações</th>
                                    <th class="text-center">Locais</th>
                                    <th class="text-center">ACI Repassada</th>
                                    <th class="text-center">ACI Mínima</th>
                                    <th class="text-center">Progresso</th>
                                    <th class="text-center">Região</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade"
    id="sinodal-modal" tabindex="-1" role="dialog"
    aria-labelledby="sinodal-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="sinodal-modalLabel">[Federação] - Formulários</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table id="federacao-entregues-table" class="table w-100">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Federação</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        </div>
        </div>
    </div>
</div>
<div class="modal fade"
    id="local-modal" tabindex="-1" role="dialog"
    aria-labelledby="local-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="local-modalLabel">[UMP Local] - Formulários</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table id="local-entregues-table" class="table w-100">
                    <thead>
                        <tr>
                            <th class="text-center">UMP Local</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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

@include('dashboard.index.partes.executivo.scripts', ['execPresidente' => true])
