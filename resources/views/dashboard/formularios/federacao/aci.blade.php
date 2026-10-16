<div class="fe-field-grid fe-cols-2">
    <x-formulario.campo-select
        name="aci[repasse]"
        label="Fez o repasse da ACI para a Sinodal?"
        :options="['N' => 'Não', 'S' => 'Sim']"
        input-id="aci[repasse]"
    />
    <x-formulario.campo-texto
        name="aci[valor]"
        label="Valor repassado"
        :value="isset($formulario) && !empty($formulario->aci['valor']) ? null : 0"
        :money="true"
    />
</div>
