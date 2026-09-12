<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="deficiencias[surdos]"
        label="Surdos"
        :value="isset($formulario) ? null : 0"
        hint="Comunica-se por LIBRAS como primeiro idioma."
        :coletor="!empty($coletorDados) ? ($coletorDados['deficiencias']['surdos'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="deficiencias[auditiva]"
        label="Deficiência auditiva"
        :value="isset($formulario) ? null : 0"
        hint="Perda parcial da percepção sonora, faz uso de aparelho auditivo."
        :coletor="!empty($coletorDados) ? ($coletorDados['deficiencias']['auditiva'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="deficiencias[cegos]"
        label="Cegos"
        :value="isset($formulario) ? null : 0"
        hint="Perda severa da visão, usa Sistema Braile e/ou orientação por voz."
        :coletor="!empty($coletorDados) ? ($coletorDados['deficiencias']['cegos'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="deficiencias[baixa_visao]"
        label="Baixa visão"
        :value="isset($formulario) ? null : 0"
        hint="Perda visual profunda ou moderada, necessidade de leitura ampliada, não usa Braile."
        :coletor="!empty($coletorDados) ? ($coletorDados['deficiencias']['baixa_visao'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="deficiencias[fisica_inferior]"
        label="Física/motora (inferiores)"
        :value="isset($formulario) ? null : 0"
        hint="Comprometimento de membros inferiores."
        :coletor="!empty($coletorDados) ? ($coletorDados['deficiencias']['fisica_inferior'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="deficiencias[fisica_superior]"
        label="Física/motora (superiores)"
        :value="isset($formulario) ? null : 0"
        hint="Comprometimento de membros superiores."
        :coletor="!empty($coletorDados) ? ($coletorDados['deficiencias']['fisica_superior'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="deficiencias[neurologico]"
        label="Transtorno neurológico"
        :value="isset($formulario) ? null : 0"
        hint="Ex.: TEA, dislexia, TDAH."
        :coletor="!empty($coletorDados) ? ($coletorDados['deficiencias']['neurologico'] ?? null) : null"
    />
    <x-formulario.campo-numero
        name="deficiencias[intelectual]"
        label="Deficiência intelectual"
        :value="isset($formulario) ? null : 0"
        hint="Ex.: Síndrome de Down, Síndrome de Angelman."
        :coletor="!empty($coletorDados) ? ($coletorDados['deficiencias']['intelectual'] ?? null) : null"
    />
</div>
@if(!isset($export))
<div class="fe-divider"></div>
<div class="fe-field-grid fe-cols-2">
    <x-formulario.campo-textarea
        name="deficiencias[outras]"
        label="Há sócios com deficiências ou necessidades não mencionadas?"
        hint="Se sim, descreva qual a deficiência ou necessidade. Se não, escreva “não”."
        placeholder="Se sim, descreva. Se não, escreva “não”."
    />
</div>
@endif
