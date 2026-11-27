@extends(isset($externo) ? 'layouts.app-externo' : 'layouts.app', [
    'export' => isset($externo)
])

@section('title', $painel['titulo'] ?? 'Game UMP')

@section('content')
<div class="painel-game-ump"
     id="painel-game-ump"
     data-url="{{ route('game-ump.acompanhamento.dados') }}">
<script type="application/json" id="game-ump-inicial">@json($painel)</script>

    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <h1 class="text-white display-4 font-weight-bold mb-2" style="font-size: 1.85rem;">
                            {{ $painel['titulo'] }}
                        </h1>
                        <p class="text-white mb-2" style="opacity: .92;">{{ $painel['subtitulo'] }}</p>
                        <p class="text-white mb-0" style="opacity: .8; font-size: .95rem;">
                            Ciclo {{ $painel['ciclo'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt--7 pb-5">
        <div class="card shadow border-0 mb-4">
            <div class="card-body py-3">
                <nav class="game-filtros" aria-label="Categoria e período">
                    <div class="game-segmentos" id="game-tipos" role="group" aria-label="Categoria"></div>
                    <div class="game-segmentos" id="game-periodos" role="group" aria-label="Exercício"></div>
                </nav>
            </div>
        </div>

        <section class="game-situacao card shadow border-0 mb-4" id="game-situacao" aria-live="polite"></section>

        <div id="game-vazio" class="card shadow border-0 mb-4" hidden>
            <div class="card-body text-center py-5" id="game-vazio-corpo"></div>
        </div>

        <div id="game-conteudo">
            <section class="card shadow border-0 mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <h2 class="h4 mb-1" id="game-pista-titulo"></h2>
                        <p class="text-muted mb-0" id="game-pista-ajuda"></p>
                    </div>
                    <div class="game-pista" id="game-pista"></div>
                </div>
            </section>

            <div class="row">
                <div class="col-lg-7" id="game-adesao-col">
                    <section class="card shadow border-0 mb-4" id="game-adesao-bloco">
                        <div class="card-body">
                            <div class="mb-3">
                                <h2 class="h4 mb-1">Cumprimento por critério</h2>
                                <p class="text-muted mb-0">Selecione um critério para ver quem ainda não completou.</p>
                            </div>
                            <ul class="game-adesao" id="game-adesao"></ul>
                        </div>
                    </section>
                </div>
                <div class="col-lg-5" id="game-prazos-col">
                    <section class="card shadow border-0 mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <h2 class="h4 mb-1">Prazos</h2>
                                <p class="text-muted mb-0">Até 23h59, horário de Brasília.</p>
                            </div>
                            <ol class="game-prazos" id="game-prazos"></ol>
                        </div>
                    </section>
                </div>
            </div>

            <section class="card shadow border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-3" style="gap:12px">
                        <div>
                            <h2 class="h4 mb-1" id="game-tabela-titulo"></h2>
                            <p class="text-muted mb-0" id="game-tabela-ajuda"></p>
                        </div>
                        <div class="game-ferramentas">
                            <label class="sr-only" for="game-busca">Buscar organização</label>
                            <input id="game-busca" type="search" class="form-control form-control-sm" placeholder="Buscar organização">
                            <label class="sr-only" for="game-liga">Liga</label>
                            <select id="game-liga" class="form-control form-control-sm"></select>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="game-exportar">Exportar CSV</button>
                        </div>
                    </div>
                    <p class="game-filtro-ativo" id="game-filtro-ativo" hidden></p>
                    <div class="table-responsive">
                        <table class="table game-tabela align-items-center" id="game-tabela"></table>
                    </div>
                    <ul class="game-legenda" id="game-legenda">
                        <li><span class="game-ponto atingido">25</span> completo</li>
                        <li><span class="game-ponto parcial">15</span> parcial</li>
                        <li><span class="game-ponto pendente">…</span> ainda em prazo</li>
                        <li><span class="game-ponto perdido">0</span> não pontuou</li>
                    </ul>
                </div>
            </section>
        </div>

        <p class="text-center text-muted mt-4 mb-2" style="font-size:.85rem;max-width:75ch;margin-left:auto;margin-right:auto;">
            A faixa de desconto é uma expectativa de benefício e não constitui direito adquirido.
            A concessão depende de deliberação da Diretoria da CNM (arts. 35 e 36 do Edital nº 02/2026).
        </p>
    </div>
</div>

<dialog id="game-detalhe" class="game-detalhe" aria-labelledby="game-detalhe-nome"></dialog>
@endsection

@push('js')
<script src="/js/game-ump-acompanhamento.js?v=20261127"></script>
@endpush
