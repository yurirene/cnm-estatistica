<div class="header bg-gradient-primary pt-2 tesouraria-panorama">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center mb-3">
                <div class="col-auto">
                    <h5 class="text-white mb-0">Panorama financeiro</h5>
                </div>
                <div class="col-auto ml-auto">
                    <div class="btn-group btn-group-sm" id="panorama-escopo" role="group" aria-label="Escopo do panorama">
                        <button type="button" class="btn btn-white" data-escopo="geral">Visão geral</button>
                        <button type="button" class="btn btn-outline-white" data-escopo="mes">Mês atual</button>
                    </div>
                </div>
            </div>

            <div
                id="panorama-dados"
                class="d-none"
                data-geral='@json($panorama['geral'])'
                data-mes='@json($panorama['mes'])'
            ></div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="exec-hero-card h-100">
                        <div class="l">Saldo inicial</div>
                        <div class="exec-trend-num">
                            <span id="val-saldo-inicial" class="n {{ $panorama['geral']['saldoInicialClasse'] }}">
                                {{ $panorama['geral']['saldoInicial'] }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="exec-hero-card h-100">
                        <div class="l">Entradas</div>
                        <div class="exec-trend-num">
                            <span id="val-entradas" class="n text-success">
                                {{ $panorama['geral']['entradas'] }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="exec-hero-card h-100">
                        <div class="l">Saídas</div>
                        <div class="exec-trend-num">
                            <span id="val-saidas" class="n text-danger">
                                {{ $panorama['geral']['saidas'] }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="exec-hero-card h-100">
                        <div class="l">Saldo atual</div>
                        <div class="exec-trend-num">
                            <span id="val-saldo-atual" class="n {{ $panorama['geral']['saldoAtualClasse'] }}">
                                {{ $panorama['geral']['saldoAtual'] }}
                            </span>
                        </div>
                        <div class="exec-hero-note">
                            @if($panorama['atualizadoAte'])
                                Atualizado até <b>{{ $panorama['atualizadoAte'] }}</b>, data do último lançamento
                            @else
                                Nenhum lançamento registrado
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="exec-hero-card mt-3">
                <div class="d-flex flex-wrap align-items-center">
                    <span class="font-weight-bold text-primary mr-4 mb-2 mb-md-0">{{ $panorama['faixa']['rotulo'] }}</span>
                    <span class="mr-4 mb-2 mb-md-0">Entradas <b class="text-success">{{ $panorama['faixa']['entradas'] }}</b></span>
                    <span class="mr-4 mb-2 mb-md-0">Saídas <b class="text-danger">{{ $panorama['faixa']['saidas'] }}</b></span>
                    <span class="mr-4 mb-2 mb-md-0">Saldo do mês <b class="{{ $panorama['faixa']['saldoClasse'] }}">{{ $panorama['faixa']['saldo'] }}</b></span>
                    <a
                        href="#lancamentos-filtros"
                        class="ml-md-auto font-weight-bold"
                        id="filtrar-mes-corrente"
                        data-mes="{{ $panorama['faixa']['valor'] }}"
                    >Ver lançamentos deste mês →</a>
                </div>
            </div>
        </div>
    </div>
</div>
