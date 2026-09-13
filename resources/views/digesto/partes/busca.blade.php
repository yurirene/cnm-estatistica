@php
    $tipoReuniaoAtual = request()->input('tipo_reuniao');
    $anoAtual = request()->input('ano');
    if (!filled($anoAtual) && count($anos_selecionados) === 1) {
        $anoAtual = $anos_selecionados[0];
    }
@endphp
<form method="GET" action="{{ route('digesto') }}" class="digesto-search-card" id="form-digesto-busca">
    <input type="hidden" name="busca" value="1">
    <div class="digesto-search-main">
        <input
            type="text"
            name="chave"
            value="{{ $chave }}"
            class="form-control"
            placeholder="Buscar por palavra-chave, número do documento ou trecho..."
            autocomplete="off"
        >
        <button type="submit" class="btn btn-primary digesto-search-btn">Buscar</button>
    </div>

    <div class="digesto-filter-row">
        <span class="digesto-filter-label">Tipo de reunião</span>
        <div class="digesto-seg">
            <label class="{{ empty($tipoReuniaoAtual) ? 'active' : '' }}">
                <input type="radio" name="tipo_reuniao" value="" @checked(empty($tipoReuniaoAtual))>
                Todas
            </label>
            @foreach($tipos as $id => $nome)
                <label class="{{ (string) $tipoReuniaoAtual === (string) $id ? 'active' : '' }}">
                    <input type="radio" name="tipo_reuniao" value="{{ $id }}" @checked((string) $tipoReuniaoAtual === (string) $id)>
                    {{ $nome }}
                </label>
            @endforeach
        </div>

        <span class="digesto-filter-group">
            <span class="digesto-filter-label">Ano</span>
            <select name="ano" id="digesto-ano" class="form-control digesto-ano-select">
                <option value="">Todos os anos</option>
                @foreach($anos_disponiveis as $ano)
                    <option value="{{ $ano }}" @selected((string) $anoAtual === (string) $ano)>{{ $ano }}</option>
                @endforeach
            </select>
        </span>
    </div>

    @foreach($tipos_selecionados as $tipoDocumento)
        <input type="hidden" name="tipos_documento[]" value="{{ $tipoDocumento }}">
    @endforeach
    @foreach($comissoes_selecionadas as $comissao)
        <input type="hidden" name="comissoes[]" value="{{ $comissao }}">
    @endforeach
    @foreach($anos_selecionados as $ano)
        @if((string) $anoAtual !== (string) $ano)
            <input type="hidden" name="anos[]" value="{{ $ano }}" class="digesto-ano-hidden">
        @endif
    @endforeach
    @if(!empty($ordenar))
        <input type="hidden" name="ordenar" value="{{ $ordenar }}">
    @endif
</form>
