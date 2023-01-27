<div class="tab-pane fade" id="segundo" role="tabpanel" aria-labelledby="segundo-tab">
    <div class="row mt-3">
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card shadow">
                                <div class="card-header">
                                    Exportar Base de Dados Excel
                                </div>
                                <div class="card-body">
                                    {!! Form::open(['method' => 'POST', 'route' => 'dashboard.estatistica.exportarExcel', 'class' => 'form-horizontal']) !!}
                                    {!! Form::select('ano_referencia', $anos_referencias, null, ['class' => 'form-control']) !!}
                                    <button class="btn btn-primary mt-2">
                                        <i class="fas fa-file-excel"></i> Exportar
                                    </button>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>