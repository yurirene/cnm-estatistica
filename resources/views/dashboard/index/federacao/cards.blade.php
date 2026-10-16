@php
    $cards = [
        [
            'label' => 'Total de Sócios',
            'value' => $totalizadores['total_socios'] ?? 0,
            'icon' => 'fas fa-users',
            'icon_bg' => 'bg-info',
        ],
        [
            'label' => 'Total de UMPs Locais',
            'value' => $totalizadores['total_umps'] ?? 0,
            'icon' => 'fas fa-users',
            'icon_bg' => 'bg-warning',
        ],
        [
            'label' => 'UMPs Locais Organizadas',
            'value' => $totalizadores['total_umps_organizadas'] ?? 0,
            'icon' => 'fas fa-check',
            'icon_bg' => 'bg-success',
        ],
        [
            'label' => 'Não utilizam o Modelo de Sociedades Internas',
            'value' => $totalizadores['total_n_sociedades_internas'] ?? 0,
            'icon' => 'fas fa-times',
            'icon_bg' => 'bg-danger',
            'alert' => ($totalizadores['total_n_sociedades_internas'] ?? 0) > 0,
        ],
    ];
@endphp
<div class="header bg-gradient-primary pb-8">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row">
                @foreach($cards as $card)
                <div class="col-xl-3 col-lg-6 mt-3">
                    <div class="card kpi-card mb-4 mb-xl-0 h-100 {{ !empty($card['alert']) ? 'alert' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="k-label">{{ $card['label'] }}</div>
                            <div class="icon icon-shape {{ $card['icon_bg'] }} text-white rounded-circle shadow">
                                <i class="{{ $card['icon'] }}"></i>
                            </div>
                        </div>
                        <div class="k-value">{{ $card['value'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
