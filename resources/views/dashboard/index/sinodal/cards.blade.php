@php
    $cards = [
        [
            'label' => 'Total de Presbitérios',
            'value' => $totalizador['total_presbiterios'] ?? 0,
            'icon' => 'fas fa-layer-group',
            'icon_bg' => 'bg-warning',
            'url' => route('dashboard.detalhamento.index', 'presbiterio'),
        ],
        [
            'label' => 'Total de Igrejas',
            'value' => $totalizador['total_igrejas'] ?? 0,
            'icon' => 'fas fa-church',
            'icon_bg' => 'bg-warning',
            'url' => route('dashboard.detalhamento.index', 'igrejas'),
        ],
        [
            'label' => 'Não utilizam o Modelo de Sociedades Internas',
            'value' => $totalizador['total_n_sociedades_internas'] ?? 0,
            'icon' => 'fas fa-times',
            'icon_bg' => 'bg-danger',
            'url' => route('dashboard.detalhamento.index', 'sem_sociedades'),
            'alert' => ($totalizador['total_n_sociedades_internas'] ?? 0) > 0,
        ],
        [
            'label' => 'Total de Sócios',
            'value' => $totalizador['total_socios'] ?? 0,
            'icon' => 'fas fa-users',
            'icon_bg' => 'bg-info',
        ],
        [
            'label' => 'Total de Federações Organizadas',
            'value' => $totalizador['total_federacoes'] ?? 0,
            'icon' => 'fas fa-sitemap',
            'icon_bg' => 'bg-success',
            'url' => route('dashboard.detalhamento.index', 'presbiterio') . '?organizadas=1',
        ],
        [
            'label' => 'Total de UMPs Locais Organizadas',
            'value' => $totalizador['total_umps'] ?? 0,
            'icon' => 'fas fa-check',
            'icon_bg' => 'bg-success',
            'url' => route('dashboard.detalhamento.index', 'igrejas') . '?organizadas=1',
        ],
    ];
@endphp
<div class="header bg-gradient-primary pb-8 pt-2">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row">
                @foreach($cards as $card)
                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="card kpi-card mb-4 mb-xl-0 h-100 {{ !empty($card['alert']) ? 'is-alert' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="k-label">{{ $card['label'] }}</div>
                            <div class="icon icon-shape {{ $card['icon_bg'] }} text-white rounded-circle shadow">
                                <i class="{{ $card['icon'] }}"></i>
                            </div>
                        </div>
                        <div class="k-value">{{ $card['value'] }}</div>
                        @if(!empty($card['url']))
                            <a href="{{ $card['url'] }}" class="k-link" style="font-size:11px;font-weight:700">
                                <i class="fas fa-plus"></i> Ver mais
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
