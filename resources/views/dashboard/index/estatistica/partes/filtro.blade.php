@php
    $anosReferencia = DashboardHelper::getAnosReferenciaFormularios();
    $anoReferenciaAtual = DashboardHelper::getAnoReferencia();
    $regioes = DashboardHelper::getRegioes();
@endphp
<div class="card shadow painel-filter-card">
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-md-3 col-lg-2">
                <div class="form-group mb-3 mb-md-0">
                    <label for="ano" class="text-muted text-uppercase ls-1 mb-1 d-block" style="font-size: .65rem;">Ano</label>
                    <select class="form-control" id="ano">
                        @foreach($anosReferencia as $ano)
                            <option value="{{ $ano }}" @selected((string) $ano === (string) $anoReferenciaAtual)>
                                {{ $ano }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <div class="form-group mb-3 mb-md-0">
                    <label for="regiao" class="text-muted text-uppercase ls-1 mb-1 d-block" style="font-size: .65rem;">Região</label>
                    <select class="form-control" id="regiao">
                        <option value="">Todas</option>
                        @foreach($regioes as $id => $nome)
                            <option value="{{ $id }}">{{ $nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-2 mb-3 mb-md-0">
                <button class="btn btn-primary" type="button" id="filtrar">Filtrar</button>
            </div>
            <div class="col-md-12 col-lg-6">
                <div class="row painel-filter-kpis">
                    <div class="col-6 col-md-3 text-lg-right">
                        <div class="painel-mini-label">Relatórios Sinodais</div>
                        <div class="painel-mini-value" id="relatorios_sinodais">—</div>
                    </div>
                    <div class="col-6 col-md-3 text-lg-right">
                        <div class="painel-mini-label">Relatórios Federações</div>
                        <div class="painel-mini-value" id="relatorios_federacoes">—</div>
                    </div>
                    <div class="col-6 col-md-3 text-lg-right">
                        <div class="painel-mini-label">Relatórios UMPs</div>
                        <div class="painel-mini-value" id="relatorios_umps_locais">—</div>
                    </div>
                    <div class="col-6 col-md-3 text-lg-right">
                        <div class="painel-mini-label">Qualidade geral</div>
                        <div class="painel-mini-value" id="qualidade_relatorio">—</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
