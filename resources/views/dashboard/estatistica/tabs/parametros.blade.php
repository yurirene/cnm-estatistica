<div class="tab-pane fade" id="primeiro" role="tabpanel" aria-labelledby="primeiro-tab">
    <div class="row mt-3">
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h2 class="text-center mb-3">Parâmetros</h2>
                        </div>
                    </div>
                    <div class="row">
                        @foreach($parametros as $parametro)
                            @include('parametros.view',$parametro)
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>