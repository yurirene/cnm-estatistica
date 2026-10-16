@props([
    'name',
    'label',
    'value' => null,
    'required' => true,
    'readonly' => false,
    'hint' => null,
    'money' => false,
    'inputId' => null,
    'placeholder' => null,
])

@php
    $opts = [
        'class' => 'fe-control form-control' . ($money ? ' isMoney' : ''),
        'autocomplete' => 'off',
    ];
    if ($required) {
        $opts['required'] = 'required';
    }
    if ($readonly) {
        $opts['readonly'] = 'readonly';
    }
    if ($inputId) {
        $opts['id'] = $inputId;
    }
    if ($placeholder) {
        $opts['placeholder'] = $placeholder;
    }
    $fieldClass = 'fe-field';
    if ($readonly) {
        $fieldClass .= ' is-readonly';
    }
    if ($errors->has($name)) {
        $fieldClass .= ' has-error';
    }
@endphp

<div class="{{ $fieldClass }}">
    <label class="fe-label" for="{{ $inputId ?: $name }}">
        <span>{{ $label }}</span>
        @if($hint)
            <x-formulario.hint :text="$hint" />
        @endif
    </label>
    {!! Form::text($name, $value, $opts) !!}
    <small class="fe-error">{{ $errors->first($name) }}</small>
</div>
