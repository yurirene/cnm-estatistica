<div class="row">
    @if(isset($filtros['estados']))
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('estados', 'Estados') !!}
            {!! Form::select(
                'estados',
                $filtros['estados'],
                null,
                ['class' => 'form-control isSelect2 filtro',  "multiple" => "multiple"]
            ) !!}
        </div>
    </div>
    @endif

    @if(isset($filtros['status']))
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('status', 'Status') !!}
            {!! Form::select(
                'status',
                $filtros['status'],
                null,
                ['class' => 'form-control filtro']
            ) !!}
        </div>
    </div>
    @endif
</div>

@push('js')
<script>


const table = $('#detalhamento-table');

table.on('preXhr.dt', function(e, settings, data){
    data.filtro = buscarDados();
});

$('.filtro').on('change', function() {
    table.DataTable().ajax.reload();
    return false;
})

function buscarDados() {
    let data = {};
    $('.filtro').each((key, value) => {
        let campo = value.getAttribute('name');
        let valor = $(value).val();
        data[campo] = valor;
    });
    return JSON.stringify(data);
}

</script>
@endpush
