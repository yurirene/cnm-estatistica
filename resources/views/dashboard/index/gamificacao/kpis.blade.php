<section class="mb-4">
    <h2 class="section-title">Painel administrativo</h2>
    <div class="kpi-grid" style="grid-template-columns: repeat({{ count($kpis) }}, minmax(0, 1fr));">
        @foreach($kpis as $kpi)
            <div class="kpi-card {{ !empty($kpi['alert']) ? 'is-alert' : '' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="k-label">{!! $kpi['label'] !!}</div>
                    <div class="icon icon-shape {{ $kpi['icon_bg'] ?? 'bg-primary' }} text-white rounded-circle shadow">
                        <i class="{{ $kpi['icon'] }}"></i>
                    </div>
                </div>
                <div class="k-value">{{ $kpi['value'] }}</div>
                @if(!empty($kpi['url']))
                    <a href="{{ $kpi['url'] }}" class="k-link" style="font-size:11px;font-weight:700">
                        <i class="fas fa-plus"></i> Ver mais
                    </a>
                @endif
            </div>
        @endforeach
    </div>
</section>
