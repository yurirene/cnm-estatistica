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
                    <div class="row mt-4">
                        <div class="col">
                            <h4 class="mb-2">Valor da ACI por ano</h4>
                            <p class="text-muted mb-3">Os cálculos usam o valor do ano da operação. O parâmetro do ano corrente atualiza só o ano de referência atual.</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="novo-ano-aci">Ano</label>
                            <input class="form-control" type="number" min="2000" id="novo-ano-aci" placeholder="Ano">
                        </div>
                        <div class="col-md-3">
                            <label for="novo-valor-aci">Valor (R$)</label>
                            <input class="form-control" type="text" id="novo-valor-aci" placeholder="24,00">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-success btn-novo-valor-aci-ano" type="button">
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="valores-aci-ano-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Ano</th>
                                    <th>Valor (R$)</th>
                                    <th class="text-center">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($valoresAciAno ?? [] as $valorAci)
                                    <tr>
                                        <td>{{ $valorAci->ano }}</td>
                                        <td>
                                            <input class="form-control form-control-sm valor-aci-ano"
                                                type="text"
                                                value="{{ number_format($valorAci->valor, 2, ',', '.') }}"
                                            >
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-success btn-sm btn-valor-aci-ano"
                                                type="button"
                                                data-ano="{{ $valorAci->ano }}"
                                            >
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>