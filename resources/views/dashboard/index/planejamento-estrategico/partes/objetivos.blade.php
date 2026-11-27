<div class="row mt-4" id="pe-objetivos">
    @foreach($painel['objetivos'] as $objetivo)
        @php
            $principal = $objetivo['indicador_principal'];
            $atual = $principal['atual'] ?? null;
            $progresso = (float) ($atual['progresso_meta_ano'] ?? 0);
            $status = $atual['status'] ?? ['classe' => 'off-track', 'label' => '—'];
        @endphp
        <div class="col-xl-4 col-lg-6 mt-3">
            <div class="card shadow h-100 pe-objective-card">
                <div class="pe-objective-header pe-cor-{{ $objetivo['cor'] }}">
                    <div class="pe-objective-number">0{{ $objetivo['id'] }}</div>
                    <div class="pe-objective-title">{{ $objetivo['titulo'] }}</div>
                </div>
                <div class="card-body">
                    <div class="pe-meta-info">
                        <div class="pe-meta-label">Meta CNM 2030</div>
                        <div class="pe-meta-value">{{ $objetivo['meta_cnm'] }}</div>
                    </div>

                    @if($principal)
                        <div class="pe-meta-info">
                            <div class="pe-meta-label">KPI principal</div>
                            <div class="pe-meta-value">{{ $principal['label'] }}</div>
                        </div>

                        <div class="pe-progress-block">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="font-weight-bold text-dark">
                                    Realizado: {{ number_format((float) ($atual['valor'] ?? 0), 1, ',', '.') }}{{ $principal['unidade'] }}
                                </span>
                                <span class="text-muted">
                                    Meta: {{ number_format((float) ($atual['meta'] ?? 0), 0, ',', '.') }}{{ $principal['unidade'] }}
                                </span>
                            </div>
                            <div class="pe-progress-bar">
                                <div class="pe-progress-fill" style="width: {{ min(100, $progresso) }}%"></div>
                            </div>
                            @if(!empty($atual['detalhe']))
                                <div class="pe-detail text-muted mt-1">{{ $atual['detalhe'] }}</div>
                            @endif
                        </div>

                        <span class="pe-status-badge {{ $status['classe'] }}">{{ $status['label'] }}</span>

                        @if(!empty($atual['pilares']))
                            <div class="pe-pilares mt-3">
                                <div class="pe-chart-title mb-2">Pilares avaliados</div>
                                @foreach($atual['pilares'] as $pilar)
                                    <div class="pe-subkpi-row">
                                        <div class="pe-subkpi-label">{{ $pilar['label'] }}</div>
                                        <div class="text-right">
                                            <strong>{{ $pilar['ok'] }}/{{ $pilar['total'] }}</strong>
                                            <div class="pe-detail text-muted">{{ number_format((float) $pilar['percentual'], 1, ',', '.') }}%</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="pe-mini-chart-wrap">
                            <div class="pe-chart-title">Evolução anual (realizado × meta)</div>
                            <div class="pe-mini-chart">
                                @foreach($painel['anos'] as $ano)
                                    @php
                                        $ponto = $principal['serie'][$ano] ?? null;
                                        $realizado = (float) ($ponto['realizado'] ?? 0);
                                        $meta = (float) ($ponto['meta'] ?? $principal['meta_2030'] ?? 100);
                                        $altura = $meta > 0 ? min(100, ($realizado / max($meta, $principal['meta_2030'] ?? $meta)) * 100) : 0;
                                        $isAtual = $ano === $painel['ano_selecionado'];
                                    @endphp
                                    <div class="pe-bar-group" title="{{ $ano }}: {{ $realizado }}% (meta {{ $meta }}%)">
                                        <div class="pe-bar {{ $isAtual ? 'active' : '' }}"
                                             style="height: {{ max(8, $altura) }}%; opacity: {{ $isAtual ? 1 : 0.55 }};">
                                            <span class="pe-bar-value">{{ number_format($realizado, 0, ',', '.') }}</span>
                                        </div>
                                        <span class="pe-bar-label">{{ $ano }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(count($objetivo['indicadores']) > 1)
                        <div class="pe-subkpis mt-3">
                            <div class="pe-chart-title mb-2">
                                {{ $objetivo['id'] === 3 ? 'Instâncias inferiores (ACI + Estatística)' : 'Outros indicadores' }}
                            </div>
                            @foreach($objetivo['indicadores'] as $indicador)
                                @continue($indicador['principal'])
                                @php $indAtual = $indicador['atual'] ?? null; @endphp
                                <div class="pe-subkpi-row">
                                    <div>
                                        <div class="pe-subkpi-label">{{ $indicador['label'] }}</div>
                                        <div class="pe-detail text-muted">{{ $indAtual['detalhe'] ?? ($indicador['descricao'] ?? '') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <strong>{{ number_format((float) ($indAtual['valor'] ?? 0), 1, ',', '.') }}{{ $indicador['unidade'] }}</strong>
                                        <div class="pe-detail text-muted">
                                            meta {{ number_format((float) ($indAtual['meta'] ?? 0), 0, ',', '.') }}{{ $indicador['unidade'] }}
                                        </div>
                                    </div>
                                </div>
                                @if(!empty($indAtual['pilares']))
                                    <div class="pe-pilares-mini mb-2">
                                        @foreach($indAtual['pilares'] as $pilar)
                                            <span class="pe-pilar-chip">
                                                {{ $pilar['label'] }}: {{ $pilar['ok'] }}/{{ $pilar['total'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
