<div class="col-xl-12 mb-5 mb-xl-0">
    <div class="card shadow p-3">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Lista de Comprovantes ACI</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            @include('includes.filtros-datatable', ['tableId' => 'comprovantes-table'])
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table w-100']) !!}
            </div>
        </div>
    </div>
</div>
