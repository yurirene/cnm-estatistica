@extends(isset($externo) ? 'layouts.app-externo' : 'layouts.app', [
    'export' => isset($externo)
])

@section('title', $painel['titulo'] ?? 'Planejamento Estratégico')

@section('content')
<div class="painel-planejamento"
     id="painel-planejamento"
     data-url="{{ route('planejamento-estrategico.dados') }}"
     data-ano="{{ $painel['ano_selecionado'] }}"
     data-regiao="{{ $painel['regiao_id'] ?? '' }}">

    @include('dashboard.index.planejamento-estrategico.partes.header')

    <div class="container-fluid mt--7 pb-5">
        @include('dashboard.index.planejamento-estrategico.partes.filtro')
        @include('dashboard.index.planejamento-estrategico.partes.overview')
        @include('dashboard.index.planejamento-estrategico.partes.objetivos')
        @include('dashboard.index.planejamento-estrategico.partes.participacoes')
        @include('dashboard.index.planejamento-estrategico.partes.tabela')

        <div class="text-center text-muted mt-5 mb-3" style="font-size: .85rem;">
            <p class="mb-1">União da Mocidade Presbiteriana · Acompanhamento do Planejamento Estratégico {{ $painel['ciclo'] }}</p>
            <p class="mb-0">Comparativo entre metas configuradas e dados dos relatórios estatísticos e da Comissão Executiva</p>
        </div>
    </div>
</div>
@endsection

@push('js')
@include('dashboard.index.planejamento-estrategico.script')
@endpush
