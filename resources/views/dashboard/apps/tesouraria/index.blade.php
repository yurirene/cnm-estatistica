@extends('layouts.app')

@section('content')

@include('dashboard.apps.tesouraria.cards',[
    'totalizadores' => $totalizadores
])


<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-pills" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active"
                                id="custom-tabs-four-home-tab"
                                data-toggle="pill"
                                href="#custom-tabs-four-home"
                                role="tab"
                                aria-controls="custom-tabs-four-home"
                                aria-selected="true">
                                Lançamento
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                id="custom-tabs-four-profile-tab"
                                data-toggle="pill"
                                href="#custom-tabs-four-profile"
                                role="tab"
                                aria-controls="custom-tabs-four-profile"
                                aria-selected="false">
                                Categoria
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                id="custom-tabs-four-relatorio-tab"
                                data-toggle="pill"
                                href="#custom-tabs-four-relatorio"
                                role="tab"
                                aria-controls="custom-tabs-four-relatorio"
                                aria-selected="false">
                                Relatório
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div
                            class="tab-pane fade show active"
                            id="custom-tabs-four-home"
                            role="tabpanel"
                            aria-labelledby="custom-tabs-four-home-tab"
                        >
                            <div class="table-responsive">
                                {!! $dataTable->table(['class' => 'table w-100']) !!}
                            </div>
                        </div>
                        <div
                            class="tab-pane fade"
                            id="custom-tabs-four-profile"
                            role="tabpanel"
                            aria-labelledby="custom-tabs-four-profile-tab"
                        >
                            @include('dashboard.apps.tesouraria.categoria')
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
@endsection

@push('js')
{!! $dataTable->scripts() !!}

<script>
$('#table-categorias').DataTable({
    dom: 'frtip',
    responsive: true,
    processing: true,
});
</script>
@endpush
