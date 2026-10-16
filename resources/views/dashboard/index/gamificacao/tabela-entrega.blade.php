<div class="card shadow">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-3" style="gap:14px">
            <h3 class="mb-0" style="font-size:14.5px;font-weight:700">Entrega de Formulários</h3>
            @if($game)
                <div style="min-width:260px">
                    <div class="d-flex justify-content-between mb-1" style="font-size:11.5px;color:var(--color-muted)">
                        <span>Reportado</span>
                        <b style="color:var(--color-bad)">{{ round($game->percentualEntrega) }}% de {{ $game->totalFilhos }} · meta {{ $game->metaEntrega }}%</b>
                    </div>
                    <div class="tp-bar">
                        <div class="fill" style="width: {{ min(100, $game->percentualEntrega) }}%"></div>
                        <div class="goal" style="left: {{ $game->metaEntrega }}%"></div>
                    </div>
                </div>
            @endif
        </div>
        <div class="table-responsive">
            <table id="formularios-entregues-table" class="table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>{{ $colunaNome ?? 'Nome' }}</th>
                        <th>Status</th>
                        <th>Impacto</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
