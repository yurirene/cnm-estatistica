@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Congresso Nacional'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Congressos Nacionais</h3>
                        </div>
                        <div class="col text-right">
                            <a href="{{ route('dashboard.congresso-nacional.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Novo Congresso Nacional
                            </a>
                            <a href="{{ route('dashboard.congresso.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar para Congressos
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {!! $dataTable->table(['class' => 'table table-striped table-bordered']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
{!! $dataTable->scripts() !!}
<script>
$(document).ready(function() {
    // Scripts específicos do congresso nacional
});
</script>
@endpush
