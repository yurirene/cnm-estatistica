<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="programacoes[social]"
        label="Cunho social"
        :value="isset($formulario) && !empty($formulario->programacoes) ? null : 0"
        hint="Ex: entrega de cestas básicas, visita a orfanatos, incluindo os projetos da Secretaria de Responsabilidade Social."
    />
    <x-formulario.campo-numero
        name="programacoes[evangelistico]"
        label="Evangelístico/missionário"
        :value="isset($formulario) && !empty($formulario->programacoes) ? null : 0"
        hint="Ex: viagem missionária, culto em praças, distribuição de folhetos, incluindo os projetos da Secretaria de Evangelismo e Missões."
    />
    <x-formulario.campo-numero
        name="programacoes[espiritual]"
        label="Cunho espiritual"
        :value="isset($formulario) && !empty($formulario->programacoes) ? null : 0"
        hint="Ex: estudo bíblico, pequenos grupos, cultos."
    />
    <x-formulario.campo-numero
        name="programacoes[recreativo]"
        label="Cunho recreativo"
        :value="isset($formulario) && !empty($formulario->programacoes) ? null : 0"
        hint="Ex: gincanas, torneio, passeios, piquenique."
    />
    <x-formulario.campo-numero
        name="programacoes[oracao]"
        label="Oração e vigílias"
        :value="isset($formulario) && !empty($formulario->programacoes) ? null : 0"
    />
</div>
