@props([
    'name',
    'label',
    'value' => null,
    'required' => false,
    'hint' => null,
    'placeholder' => null,
])

@php
    $opts = [
        'class' => 'fe-control form-control',
        'rows' => 3,
    ];
    if ($required) {
        $opts['required'] = 'required';
    }
    if ($placeholder) {
        $opts['placeholder'] = $placeholder;
    }
    $fieldClass = 'fe-field is-freetext';
    if ($errors->has($name)) {
        $fieldClass .= ' has-error';
    }
@endphp

<div class="{{ $fieldClass }}">
    <label class="fe-label">
        <span>{{ $label }}</span>
        @if($hint)
            <x-formulario.hint :text="$hint" />
        @endif
    </label>
    {!! Form::textarea($name, $value, $opts) !!}
    <small class="fe-error">{{ $errors->first($name) }}</small>
</div>
