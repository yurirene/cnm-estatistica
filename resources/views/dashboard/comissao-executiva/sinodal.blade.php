<div class="col-xl-12 mb-5 mb-xl-0">
    <div class="card shadow p-3">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">
                        Reuniões
                    </h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table w-100']) !!}
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    const TIPO_DOCUMENTO_SINODAL = 3;

    $('#tipo').on('change', function () {
        if ($(this).val() != TIPO_DOCUMENTO_SINODAL) {
            $('#titulo_titulo').text('Nome do Delegado');
            $('#titulo_doc').text('Credencial');
        } else {
            $('#titulo_titulo').text('Título');
            $('#titulo_doc').text('Documento');
        }
    });
</script>
@endpush
