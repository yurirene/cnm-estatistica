@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Detalhamento - ' . $titulo,
    'subtitulo' => $subtitulo
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Detalhamento - {{ $titulo }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('includes.filtros-datatable', ['tableId' => 'detalhamento-table'])
                    <div class="table-responsive">
                        {!! $dataTable->table() !!}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')

{!! $dataTable->scripts() !!}
@endpush
