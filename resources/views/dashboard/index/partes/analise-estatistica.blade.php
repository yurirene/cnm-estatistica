@php
    $analise = $analise ?? DashboardHelper::getAnaliseEstatistica();
    $variante = $variante ?? 'card';
    $conteudo = $analise['conteudo'] ?? [];
    $destaques = $conteudo['destaques'] ?? [];
    $atencao = $conteudo['pontos_atencao'] ?? [];
@endphp

@if($variante === 'exec')
    <div class="exec-panel h-100 analise-estatistica">
        <h4>{{ $analise['titulo'] ?? 'Análise estatística' }}</h4>
        @if($analise)
            <p class="sub mb-2">Em relação a {{ $analise['ano_anterior'] }}</p>
            <p style="font-size:13px;line-height:1.55;margin:0 0 12px;">{{ $analise['resumo'] }}</p>
            @if(count($destaques))
                <p class="sub mb-1">Destaques</p>
                <ul class="analise-lista">
                    @foreach($destaques as $item)
                        <li>{{ $item['descricao'] ?? '' }}</li>
                    @endforeach
                </ul>
            @endif
            @if(count($atencao))
                <p class="sub mb-1">Pontos de atenção</p>
                <ul class="analise-lista analise-lista-atencao">
                    @foreach($atencao as $item)
                        <li>{{ $item['descricao'] ?? '' }}</li>
                    @endforeach
                </ul>
            @endif
        @else
            <p class="sub mb-0">A análise aparece após a entrega do relatório deste ano.</p>
        @endif
    </div>
@else
    <div class="card shadow h-100 analise-estatistica">
        <div class="card-header bg-transparent">
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="mb-0">{{ $analise['titulo'] ?? 'Análise estatística' }}</h2>
                @if($analise)
                    <span class="badge badge-primary">vs {{ $analise['ano_anterior'] }}</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($analise)
                <p class="mb-3" style="font-size:13.5px;line-height:1.55;">{{ $analise['resumo'] }}</p>
                @if(count($destaques))
                    <h4 class="mb-2" style="font-size:12px;letter-spacing:.04em;text-transform:uppercase;color:var(--color-muted);">Destaques</h4>
                    <ul class="analise-lista mb-3">
                        @foreach($destaques as $item)
                            <li>{{ $item['descricao'] ?? '' }}</li>
                        @endforeach
                    </ul>
                @endif
                @if(count($atencao))
                    <h4 class="mb-2" style="font-size:12px;letter-spacing:.04em;text-transform:uppercase;color:var(--color-muted);">Pontos de atenção</h4>
                    <ul class="analise-lista analise-lista-atencao mb-0">
                        @foreach($atencao as $item)
                            <li>{{ $item['descricao'] ?? '' }}</li>
                        @endforeach
                    </ul>
                @endif
            @else
                <p class="mb-0" style="font-size:13px;color:var(--color-muted);">
                    A análise aparece após a entrega do relatório deste ano.
                </p>
            @endif
        </div>
    </div>
@endif
