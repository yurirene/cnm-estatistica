<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="estrutura[ump_organizada]"
        label="UMPs organizadas na Confederação Sinodal"
        :value="isset($formulario) && !empty($formulario->estrutura) ? null : $estrutura_sinodal['quantidade_ump']"
        :readonly="true"
        lock-note="calculado automaticamente"
        input-id="estrutura[ump_organizada]"
    />
    <x-formulario.campo-numero
        name="estrutura[ump_nao_organizada]"
        label="Igrejas sem UMPs organizadas"
        :value="isset($formulario) && !empty($formulario->estrutura) ? null : $estrutura_sinodal['quantidade_sem_ump']"
        :readonly="true"
        lock-note="calculado automaticamente"
    />
    <x-formulario.campo-numero
        name="estrutura[federacao_organizada]"
        label="Federações organizadas na Confederação Sinodal"
        :value="isset($formulario) && !empty($formulario->estrutura) ? null : $estrutura_sinodal['quantidade_federacoes']"
        :readonly="true"
        lock-note="calculado automaticamente"
        input-id="estrutura[federacao_organizada]"
    />
    <x-formulario.campo-numero
        name="estrutura[federacao_nao_organizada]"
        label="Presbitérios sem Federações organizadas"
        :value="isset($formulario) && !empty($formulario->estrutura) ? null : $estrutura_sinodal['quantidade_sem_federacao']"
    />
</div>
