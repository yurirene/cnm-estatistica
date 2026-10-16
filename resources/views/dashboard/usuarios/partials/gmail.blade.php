<hr class="my-4">
<h4 class="mb-3"><i class="fas fa-envelope"></i> Gmail</h4>
<p class="text-muted small">
    Estes filtros definem quais e-mails este usuário vê na caixa compartilhada.
    A listagem usa o alias em <code>to:</code> e o marcador em <code>label:</code>.
    Os dois podem ser preenchidos juntos.
</p>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('gmail_alias', 'Alias de e-mail') !!}
            {!! Form::email('gmail_alias', old('gmail_alias', $usuario->gmail_alias), [
                'class' => 'form-control' . ($errors->has('gmail_alias') ? ' is-invalid' : ''),
                'autocomplete' => 'off',
                'placeholder' => 'secretaria@exemplo.org',
            ]) !!}
            @error('gmail_alias')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('gmail_label', 'Marcador') !!}
            {!! Form::text('gmail_label', old('gmail_label', $usuario->gmail_label), [
                'class' => 'form-control' . ($errors->has('gmail_label') ? ' is-invalid' : ''),
                'autocomplete' => 'off',
                'maxlength' => 100,
                'placeholder' => 'Nome do marcador no Gmail',
            ]) !!}
            @error('gmail_label')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
