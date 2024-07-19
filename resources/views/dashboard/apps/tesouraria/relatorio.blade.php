<div class="row">
    <div class="col-md-12">
        <h5>Selecione o Período do Relatório</h5>
        {!! Form::open(
            [
                'url' => route('dashboard.apps.tesouraria.gerar-relatorio'),
                'method' => 'POST',
                'files' => false
            ]
        ) !!}
        <div class="row mb-2">
            <div class="col-lg-3 col-md-6">
                <label>Período</label>
                {!! Form::select(
                    'ano',
                    $anos,
                    null,
                    ['class' => 'form-control isDateRange','id' => 'ano']
                ) !!}
            </div>
            <div class="col-lg-3 col-md-6">
                <label>Listar Comprovante?</label>
                {!! Form::select(
                    'comprovante',
                    ['N' => 'Não', 'S' => 'Sim'],
                    null,
                    ['class' => 'form-control ','id' => 'comprovante']
                ) !!}
            </div>
            <div class="col-lg-3 col-md-6 d-flex align-items-end">
                <button class="btn btn-primary" type="submit" id="filtrar">Gerar</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
