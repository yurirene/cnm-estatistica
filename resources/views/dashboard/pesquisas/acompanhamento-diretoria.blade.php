@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => $pesquisa->nome
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Acompanhamento</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <a href="{{ route('dashboard.pesquisas.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                        </div>
                    </div>
                    @include('dashboard.pesquisas.partes.cards-entrega', [
                        'respostas' => $respostas
                    ])
                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <div class="card shadow h-100">
                                <div class="card-header">
                                    Lista de Sinodais que ainda não responderam
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="sinodais-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Nome</th>
                                                    <th class="text-center">Sigla</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="card shadow  h-100">
                                <div class="card-header">
                                    Lista de Federações que ainda não responderam
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table"  id="federacoes-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Nome</th>
                                                    <th class="text-center">Sigla</th>
                                                    <th class="text-center">Sinodal</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="card shadow h-100">
                                <div class="card-header">
                                    Lista de UMPs Locais que ainda não responderam
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table"  id="locais-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Nome</th>
                                                    <th class="text-center">Federacao</th>
                                                    <th class="text-center">Sinodal</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('script')
<script>
    $(function() {
        $('#sinodais-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route("dashboard.datatables.pesquisas.sinodais", $pesquisa->id) }}',
            columns: [
                {data: 'nome'},
                {data: 'sigla'},
            ]
        });
        $('#federacoes-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route("dashboard.datatables.pesquisas.federacoes", $pesquisa->id) }}',
            columns: [
                {data: 'nome'},
                {data: 'sigla'},
                {data: 'sinodal'},
            ]
        });
        $('#locais-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route("dashboard.datatables.pesquisas.locais", $pesquisa->id) }}',
            columns: [
                {data: 'nome'},
                {data: 'federacao'},
                {data: 'sinodal'},
            ]
        });
    });
</script>
@endpush