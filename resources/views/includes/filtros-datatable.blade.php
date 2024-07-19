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

    @if(isset($filtros['ano_referencia']))
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('ano_referencia', 'Ano Referência') !!}
            {!! Form::select(
                'ano_referencia',
                $filtros['ano_referencia'],
                null,
                ['class' => 'form-control filtro']
            ) !!}
        </div>
    </div>
    @endif

    @if(isset($filtros['data_criacao']))
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('data_criacao', 'Data de Cadastro') !!}
            {!! Form::text(
                'data_criacao',
                null,
                ['class' => 'form-control filtro isDateRange']
            ) !!}
        </div>
    </div>
    @endif
</div>

@push('js')
<script>


const table = $('#{{$tableId}}');

table.on('preXhr.dt', function(e, settings, data){
    data.filtro = buscarDados();
});

$('.filtro').on('change', function() {
    table.DataTable().ajax.reload();
    return false;
});

$('.filtro.isDateRange').on('apply.daterangepicker', function() {
    setTimeout(() => {
        table.DataTable().ajax.reload();
    }, 200)
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
