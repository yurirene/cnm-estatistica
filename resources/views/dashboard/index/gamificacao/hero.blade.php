@if($game)
@php
    $unidade = $game->natureza === 'sinodal' ? 'sinodais' : 'federações';
    $faixas = \App\Services\Gamificacao\GamificacaoConfiguracaoService::get("descontos.{$game->natureza}", []);
    $tetoCiclo = $game->tetoCiclo;
@endphp
<section class="game-hero mt-9">
    <div>
        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:16px">
            <div class="league-id">
                <div class="league-medal {{ $game->liga }}">
                    <i class="fas fa-medal"></i>
                </div>
                <div>
                    <div class="league-name">{{ $game->ligaLabel }}</div>
                    <div class="league-sub">{{ $game->sociosAtivos }} sócios ativos · {{ $game->faixaLiga }}</div>
                    @if(!empty($game->selos))
                        <div class="league-sub mt-1">
                            @foreach($game->selos as $selo)
                                <span class="chip ok" style="margin-right:6px">{{ $selo['label'] }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="rank-chip text-end">
                <div class="n">{{ $game->posicao ?: '—' }}º</div>
                <div class="lbl">de {{ $game->totalNaLiga ?: '—' }} {{ $unidade }}</div>
            </div>
        </div>

        <div class="mt-4">
            <div class="d-flex justify-content-between mb-2" style="font-size:13px;color:#C9D2EA">
                <span>Pontuação do ano</span>
                <b style="color:#fff;font-size:15px">{{ $game->pontosAno }} / {{ $game->tetoAno }} pts</b>
            </div>
            <div class="segbar">
                @foreach($game->segmentos as $seg)
                    @php $pct = $seg['max'] > 0 ? min(100, ($seg['pontos'] / $seg['max']) * 100) : 0; @endphp
                    <div class="seg" style="flex: {{ max(1, $seg['max']) }}">
                        <div class="fill fill-{{ $seg['cor'] }}" style="width: {{ $pct }}%"></div>
                    </div>
                @endforeach
            </div>
            <div class="seg-legend">
                @foreach($game->segmentos as $seg)
                    <span><i class="fill-{{ $seg['cor'] }}"></i>{{ $seg['label'] }} {{ $seg['max'] }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <div class="discount-panel">
        <div class="d-flex justify-content-between align-items-baseline mb-3">
            <h3 class="mb-0" style="font-size:13px;font-weight:700;color:#fff">Desconto projetado — Congresso Nacional 2030</h3>
            <span style="font-size:12px;color:#FFD8A8;font-weight:700">{{ $game->descontoProjetado }}% no ritmo atual</span>
        </div>
        @php
            $faixa50 = (int) ($faixas[50] ?? ($game->natureza === 'sinodal' ? 209 : 193));
            $faixa75 = (int) ($faixas[75] ?? ($game->natureza === 'sinodal' ? 266 : 245));
            $faixa100 = (int) ($faixas[100] ?? ($game->natureza === 'sinodal' ? 323 : 298));
        @endphp
        <div class="ladder">
            <div style="flex:{{ $faixa50 }};background:rgba(255,255,255,.14);"></div>
            <div style="flex:{{ max(1, $faixa75 - $faixa50) }};background:rgba(184,148,60,.55);"></div>
            <div style="flex:{{ max(1, $faixa100 - $faixa75) }};background:rgba(120,150,220,.6);"></div>
            <div style="flex:{{ max(1, $tetoCiclo - $faixa100) }};background:rgba(90,200,140,.65);"></div>
        </div>
        <div class="ladder-labels">
            <span>0</span>
            <span>{{ $faixa50 }} · 50%</span>
            <span>{{ $faixa75 }} · 75%</span>
            <span>{{ $faixa100 }} · 100%</span>
            <span>{{ $tetoCiclo }}</span>
        </div>
        <div class="mt-3" style="font-size:12px;color:#C9D2EA;line-height:1.5">
            @if($game->descontoProjetado >= 100)
                Desconto integral garantido no ritmo atual.
            @elseif($game->pontosParaProximoDesconto > 0)
                Faltam <b style="color:#fff">{{ $game->pontosParaProximoDesconto }} pts</b> em pontuação acumulada no ciclo para o próximo degrau de desconto.
                Pontuação anual máxima possível: <b style="color:#fff">{{ $game->tetoAno }} pts</b>.
            @else
                Próximo degrau de desconto alcançado. Continue pontuando para 100%.
            @endif
        </div>
    </div>
</section>
@endif
