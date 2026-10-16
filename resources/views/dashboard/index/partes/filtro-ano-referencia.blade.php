@php
    $anosReferencia = DashboardHelper::getAnosReferenciaFormularios();
    $anoReferenciaAtual = DashboardHelper::getAnoReferencia();
@endphp
<div class="form-group mb-0">
    <label for="filtro-ano-referencia" class="text-muted text-uppercase ls-1 mb-1 d-block" style="font-size: .65rem;">
        Ano Referência
    </label>
    <select id="filtro-ano-referencia" class="form-control" style="min-width: 120px;">
        @foreach($anosReferencia as $ano)
            <option value="{{ $ano }}" @selected((string) $ano === (string) $anoReferenciaAtual)>
                {{ $ano }}
            </option>
        @endforeach
    </select>
</div>
