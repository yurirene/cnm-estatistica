<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <h1 class="text-white display-4 font-weight-bold mb-2" style="font-size: 1.85rem;">
                        {{ $painel['titulo'] }}
                    </h1>
                    <p class="text-white mb-2" style="opacity: .92;">{{ $painel['subtitulo'] }}</p>
                    <p class="text-white mb-3" style="opacity: .8; font-size: .95rem;">
                        Escopo: <strong>{{ $painel['escopo_label'] ?? 'Brasil' }}</strong>
                    </p>
                    <div class="pe-year-timeline" id="timeline-anos">
                        @foreach($painel['anos'] as $ano)
                            <button type="button"
                                class="pe-year-btn {{ $ano === $painel['ano_selecionado'] ? 'active' : '' }}"
                                data-ano="{{ $ano }}">
                                {{ $ano }}
                                @if(!empty($painel['fases'][$ano]))
                                    <span class="pe-year-fase">{{ $painel['fases'][$ano] }}</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card shadow border-0 pe-missao-card">
                        <div class="card-body py-3">
                            <div class="text-uppercase text-muted ls-1 mb-1" style="font-size: .65rem; font-weight: 700;">
                                Fase {{ $painel['ano_selecionado'] }}
                            </div>
                            <div class="h3 mb-2 font-weight-bold" id="fase-atual-label">
                                {{ $painel['fase_atual'] ?? '—' }}
                            </div>
                            <p class="text-sm text-muted mb-0" style="line-height: 1.45;">
                                {{ \Illuminate\Support\Str::limit($painel['visao'], 160) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
