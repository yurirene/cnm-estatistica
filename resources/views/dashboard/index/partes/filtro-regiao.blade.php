@php
    $regioes = DashboardHelper::getRegioes();
@endphp
<div class="form-group mb-0">
    <label for="filtro-regiao" class="text-muted text-uppercase ls-1 mb-1 d-block" style="font-size: .65rem;">
        Região
    </label>
    <select id="filtro-regiao" class="form-control" style="min-width: 160px;">
        <option value="">Todas</option>
        @foreach($regioes as $id => $nome)
            <option value="{{ $id }}">{{ $nome }}</option>
        @endforeach
    </select>
</div>
