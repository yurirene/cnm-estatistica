@props([
    'id',
    'class' => '',
    'text' => '',
])

<div class="fe-consistency {{ $class }}" id="{{ $id }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
        <path d="M20 6L9 17l-5-5"/>
    </svg>
    <span>{{ $text }}</span>
</div>
