@php
    $regioes = \App\Helpers\DashboardHelper::getRegioes();
@endphp
<div class="card shadow painel-filter-card mb-3">
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-md-4 col-lg-3">
                <div class="form-group mb-3 mb-md-0">
                    <label for="pe-regiao" class="text-muted text-uppercase ls-1 mb-1 d-block" style="font-size: .65rem;">Região</label>
                    <select class="form-control" id="pe-regiao">
                        <option value="">Brasil (todas)</option>
                        @foreach($regioes as $id => $nome)
                            <option value="{{ $id }}" @selected((string) $id === (string) ($painel['regiao_id'] ?? ''))>
                                {{ $nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-lg-2 mb-3 mb-md-0">
                <button class="btn btn-primary" type="button" id="pe-filtrar">Filtrar</button>
            </div>
            <div class="col-md-5 col-lg-7 text-md-right">
                <div class="painel-mini-label">Escopo atual</div>
                <div class="painel-mini-value" id="pe-escopo-label">{{ $painel['escopo_label'] ?? 'Brasil' }}</div>
            </div>
        </div>
    </div>
</div>
