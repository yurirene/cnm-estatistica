<div class="row" id="pe-overview">
    @foreach($painel['overview']['cards'] as $card)
        <div class="col-xl-3 col-md-6 mt-3">
            <div class="card card-stats mb-0 h-100 pe-overview-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ $card['titulo'] }}</h5>
                            <span class="h2 font-weight-bold mb-0">
                                {{ number_format((float) $card['valor'], 1, ',', '.') }}{{ $card['unidade'] }}
                            </span>
                        </div>
                        <div class="col-auto">
                            @php
                                $classe = $card['status']['classe'] ?? 'at-risk';
                                $iconBg = $classe === 'on-track' ? 'bg-success' : ($classe === 'at-risk' ? 'bg-warning' : 'bg-danger');
                            @endphp
                            <div class="icon icon-shape {{ $iconBg }} text-white rounded-circle shadow">
                                <i class="fas fa-bullseye"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-nowrap">
                            Meta {{ $painel['ano_selecionado'] }}:
                            <strong>{{ number_format((float) $card['meta'], 0, ',', '.') }}{{ $card['unidade'] }}</strong>
                        </span>
                        <span class="text-muted d-block">
                            Meta 2030: {{ number_format((float) $card['meta_2030'], 0, ',', '.') }}{{ $card['unidade'] }}
                        </span>
                    </p>
                    @if(!empty($card['status']))
                        <span class="pe-status-badge {{ $card['status']['classe'] }}">
                            {{ $card['status']['label'] }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
