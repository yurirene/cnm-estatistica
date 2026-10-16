@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
    'required' => true,
    'hint' => null,
    'inputId' => null,
])

@php
    $opts = [
        'class' => 'fe-control form-control',
        'autocomplete' => 'off',
    ];
    if ($required) {
        $opts['required'] = 'required';
    }
    if ($inputId) {
        $opts['id'] = $inputId;
    }
    $fieldClass = 'fe-field';
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
    {!! Form::select($name, $options, $value, $opts) !!}
    <small class="fe-error">{{ $errors->first($name) }}</small>
</div>
