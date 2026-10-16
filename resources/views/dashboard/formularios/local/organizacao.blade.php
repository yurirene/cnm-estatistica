<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="organizacao[treinamentos_participados_federacao]"
        label="Treinamentos da Federação"
        :value="isset($formulario) && !empty($formulario->organizacao) ? null : 0"
    />
    <x-formulario.campo-numero
        name="organizacao[treinamentos_participados_sinodal]"
        label="Treinamentos da Sinodal"
        :value="isset($formulario) && !empty($formulario->organizacao) ? null : 0"
    />
    <x-formulario.campo-numero
        name="organizacao[treinamentos_participados_cnm]"
        label="Treinamentos da CNM"
        :value="isset($formulario) && !empty($formulario->organizacao) ? null : 0"
    />
</div>
