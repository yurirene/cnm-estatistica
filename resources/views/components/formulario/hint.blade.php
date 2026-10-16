@props(['text'])

<button type="button" class="fe-hint" aria-label="Ajuda: {{ $text }}">
    i
    <span class="fe-tooltip" role="tooltip">{{ $text }}</span>
</button>
