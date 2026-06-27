<hr class="my-4">
<h4 class="mb-3"><i class="fab fa-google-drive"></i> Google Drive</h4>
<p class="text-muted small">
    A pasta principal do sistema fica no <code>.env</code> (<code>GOOGLE_DRIVE_ROOT_FOLDER_ID</code>),
    compartilhada com a conta de serviço como <strong>Editor</strong>.
    Ao salvar, se o campo abaixo estiver vazio, a pasta deste usuário será criada automaticamente
    dentro da pasta principal.
</p>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('google_drive_folder', 'ID da pasta do usuário (opcional)') !!}
            {!! Form::text('google_drive_folder', old('google_drive_folder', $usuario->google_drive_folder), [
                'class' => 'form-control',
                'autocomplete' => 'off',
                'placeholder' => $usuario->google_drive_folder ? $usuario->google_drive_folder : 'Deixe vazio para criar automaticamente',
            ]) !!}
            <small class="form-text text-muted">
                @if($usuario->google_drive_folder)
                    Pasta atual: <code>{{ $usuario->google_drive_folder }}</code>.
                    Informe outro ID apenas se quiser substituir manualmente.
                @else
                    Será criada dentro da pasta principal ao salvar o usuário.
                @endif
            </small>
        </div>
    </div>
</div>
