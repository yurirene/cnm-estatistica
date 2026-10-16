@if($game)
<div class="card shadow h-100">
    <div class="card-header bg-transparent">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="mb-0">Ranking</h2>
            <span class="info-row"><span class="k">LIGA</span></span>
        </div>
    </div>
    <div class="card-body text-center">
        <div class="league-medal {{ $game->liga }} mx-auto mb-2">
            <i class="fas fa-medal"></i>
        </div>
        <div style="font-size:26px;font-weight:800">{{ $game->posicao ?: '—' }}º</div>
        <div style="font-size:12px;color:var(--color-muted)">
            de {{ $game->totalNaLiga ?: '—' }} {{ $game->natureza === 'sinodal' ? 'sinodais' : 'federações' }}
        </div>
        <div class="chip {{ $game->liga === 'ouro' ? 'ok' : ($game->liga === 'prata' ? 'pendente' : 'indisponivel') }} mt-2">
            {{ $game->ligaLabel }}
        </div>
        <div class="mt-3 pt-3" style="border-top:1px solid var(--color-line);font-size:11.5px;color:var(--color-muted)">
            {{ $game->tendenciaRanking }}
        </div>
    </div>
</div>
@endif
