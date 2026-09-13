<aside class="digesto-facet-panel">
    @if(!empty($facets['tipos_documento']))
        <div class="digesto-facet-group">
            <div class="digesto-facet-title">Tipo de documento</div>
            @foreach($facets['tipos_documento'] as $item)
                <a href="{{ $item['url'] }}" class="digesto-facet-item {{ $item['ativo'] ? 'checked' : '' }}">
                    <span class="name">
                        <span class="digesto-check" aria-hidden="true"></span>
                        {{ $item['label'] }}
                    </span>
                    <span class="digesto-facet-count">{{ $item['total'] }}</span>
                </a>
            @endforeach
        </div>
    @endif

    @if(!empty($facets['anos']))
        <div class="digesto-facet-group">
            <div class="digesto-facet-title">Ano</div>
            @foreach($facets['anos'] as $item)
                <a href="{{ $item['url'] }}" class="digesto-facet-item {{ $item['ativo'] ? 'checked' : '' }}">
                    <span class="name">
                        <span class="digesto-check" aria-hidden="true"></span>
                        {{ $item['label'] }}
                    </span>
                    <span class="digesto-facet-count">{{ $item['total'] }}</span>
                </a>
            @endforeach
        </div>
    @endif

    @if(!empty($facets['comissoes']))
        <div class="digesto-facet-group">
            <div class="digesto-facet-title">Comissão</div>
            @foreach($facets['comissoes'] as $item)
                <a href="{{ $item['url'] }}" class="digesto-facet-item {{ $item['ativo'] ? 'checked' : '' }}">
                    <span class="name">
                        <span class="digesto-check" aria-hidden="true"></span>
                        {{ $item['label'] }}
                    </span>
                    <span class="digesto-facet-count">{{ $item['total'] }}</span>
                </a>
            @endforeach
        </div>
    @endif
</aside>
