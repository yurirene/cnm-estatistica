@props([
    'name',
    'label',
    'value' => null,
    'readonly' => false,
    'required' => true,
    'hint' => null,
    'coletor' => null,
    'lockNote' => null,
    'inputId' => null,
    'span' => null,
])

@php
    $opts = [
        'class' => 'fe-stepper-input form-control',
        'min' => '0',
        'step' => '1',
        'autocomplete' => 'off',
        'inputmode' => 'numeric',
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
    $fieldClass = 'fe-field';
    if ($readonly) {
        $fieldClass .= ' is-readonly';
    }
    if ($errors->has($name)) {
        $fieldClass .= ' has-error';
    }
    if ($span) {
        $fieldClass .= ' ' . $span;
    }
@endphp

<div class="{{ $fieldClass }}">
    <label class="fe-label" for="{{ $inputId ?: $name }}">
        <span>{{ $label }}</span>
        @if($hint)
            <x-formulario.hint :text="$hint" />
        @endif
    </label>
    <div class="fe-stepper">
        <button type="button" class="fe-stepper-btn" data-fe-step="-1" @disabled($readonly) aria-label="Diminuir">–</button>
        {!! Form::number($name, $value, $opts) !!}
        <button type="button" class="fe-stepper-btn" data-fe-step="1" @disabled($readonly) aria-label="Aumentar">+</button>
    </div>
    @if($readonly && $lockNote)
        <span class="fe-lock-note">{{ $lockNote }}</span>
    @endif
    @if($coletor !== null && $coletor !== '')
        <small class="fe-coletor">Informação do coletor de dados: {{ $coletor }}</small>
    @endif
    <small class="fe-error">{{ $errors->first($name) }}</small>
</div>
