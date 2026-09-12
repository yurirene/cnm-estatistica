<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="escolaridade[fundamental]"
        label="Ensino Fundamental"
        :value="isset($formulario) ? null : 0"
        hint="Sócios que ainda estão cursando o ensino médio ou não fizeram o ensino médio."
        :coletor="!empty($coletorDados) ? ($coletorDados['escolaridade']['fundamental'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="escolaridade[medio]"
        label="Ensino Médio"
        :value="isset($formulario) ? null : 0"
        hint="Sócios que concluíram o ensino médio ou sócios que ainda não concluíram o ensino superior."
        :coletor="!empty($coletorDados) ? ($coletorDados['escolaridade']['medio'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="escolaridade[tecnico]"
        label="Ensino Técnico"
        :value="isset($formulario) ? null : 0"
        hint="Sócios que concluíram o ensino médio técnico mas não concluíram o ensino superior. Se concluiu o ensino superior não adicione aqui."
        :coletor="!empty($coletorDados) ? ($coletorDados['escolaridade']['tecnico'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="escolaridade[superior]"
        label="Ensino Superior"
        :value="isset($formulario) ? null : 0"
        hint="Sócios que concluíram o ensino superior ou ainda não concluíram a Pós-Graduação."
        :coletor="!empty($coletorDados) ? ($coletorDados['escolaridade']['superior'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="escolaridade[pos]"
        label="Pós-Graduação"
        :value="isset($formulario) ? null : 0"
        hint="Sócios que concluíram ao menos uma pós-graduação. Se ainda está cursando, contabilize em ensino superior."
        :coletor="!empty($coletorDados) ? ($coletorDados['escolaridade']['pos'] ?? null) : null"
    />
</div>
<x-formulario.consistencia id="fe-consist-escolaridade" text="Total por escolaridade será conferido com o total de sócios." />
