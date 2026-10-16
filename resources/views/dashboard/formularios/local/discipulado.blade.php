<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="discipulado[trilha_cnm]"
        label="Trilha da CNM"
        :value="isset($formulario) && !empty($formulario->discipulado) ? null : 0"
        :readonly="true"
        lock-note="calculado automaticamente"
    />
    <x-formulario.campo-numero
        name="discipulado[discipulando_cnm]"
        label="Método adotado pela CNM"
        :value="isset($formulario) && !empty($formulario->discipulado) ? null : 0"
        :readonly="true"
        lock-note="calculado automaticamente"
    />
    <x-formulario.campo-numero
        name="discipulado[discipulando_outro]"
        label="Outro método que não seja o da CNM"
        :value="isset($formulario) && !empty($formulario->discipulado) ? null : 0"
    />
    <x-formulario.campo-numero
        name="discipulado[sendo_discipulados]"
        label="Jovens da UMP sendo discipulados"
        :value="isset($formulario) && !empty($formulario->discipulado) ? null : 0"
    />
</div>
