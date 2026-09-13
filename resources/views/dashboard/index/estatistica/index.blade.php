
@extends(isset($externo) ? 'layouts.app-externo' : 'layouts.app', [
    'export' => isset($externo)
])
@section('content')
    @include('dashboard.index.estatistica.cards')

    <div class="container-fluid mt--7 painel-estatistica">
        @include('dashboard.index.estatistica.partes.filtro')

        <ul class="nav nav-tabs painel-tabs mt-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#tab-geral" data-tab="geral">Visão Geral</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tab-perfil" data-tab="perfil">Perfil dos Sócios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tab-espiritualidade" data-tab="espiritualidade">Espiritualidade e Programações</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tab-inclusao" data-tab="inclusao">Inclusão</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tab-financeiro" data-tab="financeiro">Estrutura e Financeiro</a>
            </li>
        </ul>

        <div class="painel-tab-pane active" id="tab-geral" data-tab-pane="geral">
            @include('dashboard.index.estatistica.partes.visao-geral')
        </div>
        <div class="painel-tab-pane" id="tab-perfil" data-tab-pane="perfil">
            @include('dashboard.index.estatistica.partes.perfil')
        </div>
        <div class="painel-tab-pane" id="tab-espiritualidade" data-tab-pane="espiritualidade">
            @include('dashboard.index.estatistica.partes.espiritualidade')
        </div>
        <div class="painel-tab-pane" id="tab-inclusao" data-tab-pane="inclusao">
            @include('dashboard.index.estatistica.partes.inclusao')
        </div>
        <div class="painel-tab-pane" id="tab-financeiro" data-tab-pane="financeiro">
            @include('dashboard.index.estatistica.partes.financeiro')
        </div>
    </div>
@endsection

@push('js')
@include('dashboard.index.estatistica.script')
@endpush
