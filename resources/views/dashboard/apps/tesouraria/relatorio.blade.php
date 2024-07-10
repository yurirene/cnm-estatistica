<div class="row">
    <div class="col-md-12">
        <h5><i class="fas fa-filter"></i> Filtros</h5>
        {!! Form::open(
            [
                'url' => route('dashboard.apps.tesouraria.gerar-relatorio'),
                'method' => 'POST',
                'files' => false
            ]
        ) !!}
        <div class="row mb-2">
            <div class="col-lg-3 col-md-6">
                <label>Data Lançamento</label>
                {!! Form::text(
                    'periodo',
                    null,
                    ['class' => 'form-control isDateRange','id' => 'periodo']
                ) !!}
            </div>
            <div class="col-lg-3 col-md-6">
                <label>Tipo</label>
                {!! Form::select(
                    'tipo_relatorio',
                    ['relatorio' => 'Relatório CSV', 'comprovantes' => 'Baixar Comprovantes'],
                    null,
                    ['class' => 'form-control', 'id' => 'tipo_relatorio']
                ) !!}
            </div>
            <div class="col-lg-3 col-md-6 d-flex align-items-end">
                <button class="btn btn-primary" type="submit" id="filtrar">Gerar</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
