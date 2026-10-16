@if(!empty($chips))
    <div class="digesto-chips">
        @foreach($chips as $chip)
            <a href="{{ $chip['url'] }}" class="digesto-chip">
                {{ $chip['label'] }}
                <span class="x" aria-hidden="true">&times;</span>
            </a>
        @endforeach
    </div>
@endif

<div class="digesto-results-header">
    <div class="digesto-results-count">
        @if($chave !== '')
            <b>{{ $total }}</b> {{ $total === 1 ? 'resultado' : 'resultados' }} para <b>"{{ $chave }}"</b>
        @else
            <b>{{ $total }}</b> {{ $total === 1 ? 'resultado' : 'resultados' }}
        @endif
    </div>
    <div class="digesto-sort">
        <label for="digesto-ordenar">Ordenar por</label>
        <select id="digesto-ordenar" class="form-control">
            <option value="{{ \App\Services\DigestoService::urlCom(['ordenar' => 'relevancia']) }}" @selected($ordenar === 'relevancia')>
                Relevância
            </option>
            <option value="{{ \App\Services\DigestoService::urlCom(['ordenar' => 'recente']) }}" @selected($ordenar === 'recente')>
                Mais recente
            </option>
            <option value="{{ \App\Services\DigestoService::urlCom(['ordenar' => 'numero']) }}" @selected($ordenar === 'numero')>
                Número do documento
            </option>
        </select>
    </div>
</div>

@if($total > 0)
    <div class="digesto-export">
        <a href="{{ route('digesto.exportar', request()->query()) }}" class="digesto-rbtn">
            <i class="fas fa-download"></i>
            Exportar resultados
        </a>
    </div>
@endif

@forelse($resultados as $dado)
    <article class="digesto-result-card">
        <div class="digesto-result-top">
            @if($dado->tipo_documento_label)
                <span class="digesto-type-badge {{ $dado->tipo_documento_classe }}">{{ $dado->tipo_documento_label }}</span>
            @endif
            <span class="digesto-meeting">
                <i class="far fa-calendar-alt"></i>
                {{ $dado->tipo_formatado }}@if($dado->ano) · {{ $dado->ano }}@endif
            </span>
            @if($dado->numero_documento)
                <span class="digesto-doc-num">Doc. {{ $dado->numero_documento }}</span>
            @endif
        </div>
        <h3 class="digesto-result-title">{!! $dado->titulo_html !!}</h3>
        <p class="digesto-result-snippet">{!! $dado->snippet !!}</p>
        <div class="digesto-result-actions">
            <a href="{{ route('digesto.exibir', $dado->path_exibir) }}" target="_blank" class="digesto-rbtn primary">
                <i class="far fa-eye"></i>
                Ver documento
            </a>
            <button type="button" class="digesto-rbtn js-copiar-citacao" data-citacao="{{ $dado->citacao }}">
                <i class="far fa-copy"></i>
                Copiar citação
            </button>
        </div>
    </article>
@empty
    <div class="digesto-empty">
        Nenhum documento encontrado para os filtros selecionados.
    </div>
@endforelse

@if($resultados->hasPages())
    <div class="digesto-pagination">
        {{ $resultados->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
@endif
