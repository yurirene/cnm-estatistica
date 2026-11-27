@if(!empty($painel['participacoes']))
<div class="row mt-4" id="pe-participacoes">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-transparent border-0 pb-0">
                <h3 class="mb-1">Participações e compromissos complementares</h3>
                <p class="text-muted mb-0 text-sm">Indicadores fora dos três objetivos, usados no acompanhamento institucional (ex.: Comissão Executiva).</p>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($painel['participacoes'] as $participacao)
                        @php
                            $atual = $participacao['atual'] ?? null;
                            $progresso = (float) ($atual['progresso_meta_ano'] ?? 0);
                            $status = $atual['status'] ?? ['classe' => 'off-track', 'label' => '—'];
                        @endphp
                        <div class="col-lg-6 mt-2">
                            <div class="pe-participacao-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h4 class="mb-1">{{ $participacao['label'] }}</h4>
                                        <p class="text-muted text-sm mb-0">{{ $participacao['descricao'] }}</p>
                                    </div>
                                    <span class="pe-status-badge {{ $status['classe'] }}">{{ $status['label'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="h2 mb-0 font-weight-bold">
                                        {{ number_format((float) ($atual['valor'] ?? 0), 1, ',', '.') }}{{ $participacao['unidade'] }}
                                    </span>
                                    <span class="text-muted align-self-end">
                                        Meta {{ $painel['ano_selecionado'] }}:
                                        {{ number_format((float) ($atual['meta'] ?? 0), 0, ',', '.') }}{{ $participacao['unidade'] }}
                                    </span>
                                </div>
                                <div class="pe-progress-bar mb-2">
                                    <div class="pe-progress-fill" style="width: {{ min(100, $progresso) }}%"></div>
                                </div>
                                @if(!empty($atual['detalhe']))
                                    <div class="pe-detail text-muted">{{ $atual['detalhe'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif
