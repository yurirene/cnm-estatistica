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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table id="sinodais-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Data</th>
                                            <th class="text-center">Erro</th>
                                            <th class="text-center">Usuário Afetado</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table id="federacoes-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Data</th>
                                            <th class="text-center">Erro</th>
                                            <th class="text-center">Usuário Afetado</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table id="locais-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Data</th>
                                            <th class="text-center">Erro</th>
                                            <th class="text-center">Usuário Afetado</th>
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
@endsection

@push('script')
<script>
    $(function() {
        $('#sinodais-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route("dashboard.datatables.pesquisas.sinodais") }}',
            columns: [
                {
                    render: function (data, type, result) {
                        return `<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#log-erro-modal" data-dia="${result.dia}" data-informacoes="${result.erro_completo}"><i class="fas fa-eye"></i></button>`;
                    }
                },
                {data: 'dia'},
                {data: 'erro'},
                {data: 'usuario'},
            ]
        });
        $('#federacoes-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route("dashboard.datatables.pesquisas.federacoes") }}',
            columns: [
                {
                    render: function (data, type, result) {
                        return `<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#log-erro-modal" data-dia="${result.dia}" data-informacoes="${result.erro_completo}"><i class="fas fa-eye"></i></button>`;
                    }
                },
                {data: 'dia'},
                {data: 'erro'},
                {data: 'usuario'},
            ]
        });
        $('#locais-table').DataTable({
            dom: 'frtip',
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{{ route("dashboard.datatables.pesquisas.locais") }}',
            columns: [
                {
                    render: function (data, type, result) {
                        return `<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#log-erro-modal" data-dia="${result.dia}" data-informacoes="${result.erro_completo}"><i class="fas fa-eye"></i></button>`;
                    }
                },
                {data: 'dia'},
                {data: 'erro'},
                {data: 'usuario'},
            ]
        });
    });
</script>
@endpush