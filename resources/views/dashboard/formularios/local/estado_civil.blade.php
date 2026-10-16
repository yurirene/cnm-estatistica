<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="estado_civil[solteiros]"
        label="Solteiros"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['estado_civil']['solteiros'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="estado_civil[casados]"
        label="Casados"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['estado_civil']['casados'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="estado_civil[divorciados]"
        label="Divorciados"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['estado_civil']['divorciados'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="estado_civil[viuvos]"
        label="Viúvos"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['estado_civil']['viuvos'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="estado_civil[filhos]"
        label="Sócios com filhos"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['estado_civil']['filhos'] ?? null) : null"
    />
</div>
<x-formulario.consistencia id="fe-consist-estado-civil" text="Total por estado civil será conferido com o total de sócios." />
<x-formulario.consistencia id="fe-consist-filhos" text="A quantidade de sócios com filhos não pode ultrapassar o total de sócios." />
