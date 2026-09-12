<div class="fe-field-grid">
    <x-formulario.campo-numero
        name="estrutura[ump_organizada]"
        label="UMPs organizadas na Federação"
        :value="isset($formulario) && !empty($formulario->estrutura) ? null : $estrutura_federacao['quantidade_umps']"
        :readonly="true"
        lock-note="calculado automaticamente"
    />
    <x-formulario.campo-numero
        name="estrutura[ump_nao_organizada]"
        label="Presbitério sem UMPs"
        :value="isset($formulario) && !empty($formulario->estrutura) ? null : $estrutura_federacao['quantidade_sem_ump']"
    />
    <x-formulario.campo-numero
        name="estrutura[nro_repasse]"
        label="UMPs com repasse da ACI"
        :value="isset($formulario) && !empty($formulario->estrutura) ? null : $estrutura_federacao['nro_repasse']"
    />
    <x-formulario.campo-numero
        name="estrutura[nro_sem_repasse]"
        label="UMPs sem repasse da ACI"
        :value="isset($formulario) && !empty($formulario->estrutura) ? null : $estrutura_federacao['nro_sem_repasse']"
    />
</div>
