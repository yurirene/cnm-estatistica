@php $ranking = DashboardHelper::getRankingUrgencia(); @endphp
<div class="exec-panel h-100 exec-ranking">
    <h4>Ranking de urgência</h4>
    <div class="sub">Ordenado por menor taxa de resposta — não por ordem alfabética.</div>
    <div class="exec-ranking-list">
        @forelse($ranking as $item)
            <div class="exec-action-row">
                <div class="left">
                    <span class="exec-sev-dot {{ $item['severidade'] }}"></span>
                    <div>
                        <div class="exec-action-name">{{ $item['nome'] }}</div>
                        <div class="exec-action-meta">{{ $item['meta'] }}</div>
                    </div>
                </div>
                @if(!empty($item['whatsapp']))
                    <a class="row-action" href="{{ $item['whatsapp'] }}" target="_blank" rel="noopener noreferrer">Lembrar</a>
                @endif
            </div>
        @empty
            <div class="exec-hero-note mb-0">Nenhuma sinodal pendente no momento.</div>
        @endforelse
    </div>
</div>
