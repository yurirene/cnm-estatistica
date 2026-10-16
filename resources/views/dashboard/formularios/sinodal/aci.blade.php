<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="aci[ump_repassaram]"
        label="UMPs que fizeram o repasse da ACI"
        :value="isset($formulario) && !empty($formulario->aci) ? null : $estrutura_sinodal['quantidade_ump_repasse']"
        :readonly="true"
        lock-note="calculado automaticamente"
        input-id="aci[ump_repassaram]"
    />
    <x-formulario.campo-numero
        name="aci[ump_nao_repassaram]"
        label="UMPs que não fizeram o repasse da ACI"
        :value="isset($formulario) && !empty($formulario->aci) ? null : $estrutura_sinodal['quantidade_ump_sem_repasse']"
        :readonly="true"
        lock-note="calculado automaticamente"
    />
    <x-formulario.campo-numero
        name="aci[federacao_repassaram]"
        label="Federações que fizeram o repasse da ACI"
        :value="isset($formulario) && !empty($formulario->aci) ? null : $estrutura_sinodal['federacao_nro_repasse']"
        input-id="aci[federacao_repassaram]"
    />
    <x-formulario.campo-numero
        name="aci[federacao_nao_repassaram]"
        label="Federações que não fizeram o repasse da ACI"
        :value="isset($formulario) && !empty($formulario->aci) ? null : $estrutura_sinodal['federacao_nro_sem_repasse']"
    />
</div>
<div class="fe-divider"></div>
<div class="fe-field-grid fe-cols-2">
    <x-formulario.campo-select
        name="aci[repasse]"
        label="A Sinodal fez o repasse da ACI para a CNM?"
        :options="['N' => 'Não', 'S' => 'Sim']"
        :value="isset($formulario) ? null : 'N'"
        input-id="aci[repasse]"
    />
    <x-formulario.campo-texto
        name="aci[valor_repassado]"
        label="Valor do repasse da ACI para a CNM"
        :value="isset($formulario) && !empty($formulario->aci) ? null : 0"
        :money="true"
    />
</div>
