<h4 class="fe-subsection">Tipo</h4>
<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="perfil[ativos]"
        label="Sócios ativos"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['perfil']['ativos'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="perfil[cooperadores]"
        label="Sócios cooperadores"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['perfil']['cooperadores'] ?? null) : null"
    />
</div>

<div class="fe-divider"></div>
<h4 class="fe-subsection">Faixa etária</h4>
<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="perfil[menor19]"
        label="Menores de 19"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['perfil']['menor19'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="perfil[de19a23]"
        label="19–23 anos"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['perfil']['de19a23'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="perfil[de24a29]"
        label="24–29 anos"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['perfil']['de24a29'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="perfil[de30a35]"
        label="30–35 anos"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['perfil']['de30a35'] ?? null) : null"
    />
</div>
<x-formulario.consistencia id="fe-consist-idade" text="Total por faixa etária será conferido com o total de sócios." />

<div class="fe-divider"></div>
<h4 class="fe-subsection">Gênero</h4>
<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="perfil[homens]"
        label="Homens"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['perfil']['homens'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="perfil[mulheres]"
        label="Mulheres"
        :value="isset($formulario) ? null : 0"
        :coletor="!empty($coletorDados) ? ($coletorDados['perfil']['mulheres'] ?? null) : null"
    />
</div>
<x-formulario.consistencia id="fe-consist-genero" text="Total por gênero será conferido com o total de sócios." />
