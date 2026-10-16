@if($game && count($game->pilares))
<section class="mb-4">
    <h2 class="section-title">Como sua {{ $game->natureza === 'sinodal' ? 'sinodal' : 'federação' }} pontua este ano</h2>
    <p class="section-sub">Pilares do Planejamento Estratégico · prazos e status atualizados em tempo real</p>
    <div class="pillar-grid">
        @foreach($game->pilares as $pilar)
            <div class="pillar-card {{ $pilar['status'] }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="d-flex align-items-start" style="gap:6px">
                            <div style="font-size:12.5px;font-weight:700;line-height:1.3">{{ $pilar['label'] }}</div>
                        </div>
                        <div style="font-size:11px;color:var(--color-muted);margin-top:2px">
                            {{ str_starts_with($pilar['pilar'], 'bonus') || $pilar['pilar'] !== 'evangelismo' ? '+' : 'até ' }}{{ $pilar['pontos_maximo'] }} pts
                        </div>
                    </div>
                    <span class="chip {{ $pilar['status'] }}">{{ $pilar['rotulo_status'] }}</span>
                </div>
                <div class="pbar">
                    <div class="fill-{{ $pilar['progresso'] == 100 ? 'green' : $pilar['cor'] }}" style="width: {{ $pilar['progresso'] }}%"></div>
                </div>
                <div style="font-size:11px;color:var(--color-muted);line-height:1.4">
                    {{ $pilar['rodape'] }}
                    @if(!empty($pilar['hint']))
                        <button type="button" class="pillar-hint" aria-label="Ajuda: {{ $pilar['hint'] }}">
                            i
                            <span class="pillar-hint-tooltip" role="tooltip">{{ $pilar['hint'] }}</span>
                        </button>
                    @endif
                </div>
                @if(!empty($pilar['cta_label']) && !empty($pilar['cta_rota']))
                    <a class="mt-1" href="{{ route($pilar['cta_rota']) }}" style="font-size:11.5px;font-weight:700">
                        {{ $pilar['cta_label'] }} →
                    </a>
                @endif
            </div>
        @endforeach
    </div>
</section>
@push('js')
<script>
    $(document).on('click', '.pillar-hint', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $this = $(this);
        $('.pillar-hint').not($this).removeClass('is-open');
        $this.toggleClass('is-open');
    });
    $(document).on('click', function (e) {
        if ($(e.target).closest('.pillar-hint').length) {
            return;
        }
        $('.pillar-hint').removeClass('is-open');
    });
</script>
@endpush
@endif
