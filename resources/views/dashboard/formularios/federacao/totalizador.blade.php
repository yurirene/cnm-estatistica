@php
    $abaixoMinimo = $qualidade_entrega['porcentagem'] < $qualidade_entrega['minimo'];
@endphp

<div class="fe-hero-row">
    <div class="fe-hero-card">
        <div class="fe-hero-label">Formulários entregues pelas UMPs locais</div>
        <div class="fe-hero-big">{{ $qualidade_entrega['porcentagem'] }}%</div>
        <span class="fe-status-pill {{ $abaixoMinimo ? 'is-warn' : 'is-good' }}">
            {{ $qualidade_entrega['texto'] }}
        </span>
        <div class="fe-hero-note">Ano referência {{ $ano_referencia }}.</div>
    </div>
    <div class="fe-hero-card">
        <div class="fe-hero-label">ACI recebida no ano</div>
        <div class="fe-hero-big">R$ <span id="aci-recebida">—</span></div>
        <div class="fe-hero-note">Valores consolidados das UMPs locais.</div>
    </div>
</div>

<x-formulario.graficos prefix="vigente" />

<div class="fe-panels">
    <div class="fe-panel">
        <h4>Perfil</h4>
        <div class="fe-mini-list">
            <div class="fe-mini-row"><span>Ativos</span><span class="fe-mini-val"><b id="resumo-ativos"></b></span></div>
            <div class="fe-mini-row"><span>Cooperadores</span><span class="fe-mini-val"><b id="resumo-cooperadores"></b></span></div>
            <div class="fe-mini-row"><span>Homens</span><span class="fe-mini-val"><b id="resumo-homens"></b></span></div>
            <div class="fe-mini-row"><span>Mulheres</span><span class="fe-mini-val"><b id="resumo-mulheres"></b></span></div>
            <div class="fe-mini-row"><span>Menor de 19</span><span class="fe-mini-val"><b id="resumo-menor19"></b></span></div>
            <div class="fe-mini-row"><span>19 a 23</span><span class="fe-mini-val"><b id="resumo-de19a23"></b></span></div>
            <div class="fe-mini-row"><span>24 a 29</span><span class="fe-mini-val"><b id="resumo-de24a29"></b></span></div>
            <div class="fe-mini-row"><span>30 a 35</span><span class="fe-mini-val"><b id="resumo-de30a35"></b></span></div>
        </div>
    </div>
    <div class="fe-panel">
        <h4>Escolaridade</h4>
        <div class="fe-mini-list">
            <div class="fe-mini-row"><span>Até o Ens. Fundamental</span><span class="fe-mini-val"><b id="resumo-fundamental"></b></span></div>
            <div class="fe-mini-row"><span>Até o Ens. Médio</span><span class="fe-mini-val"><b id="resumo-medio"></b></span></div>
            <div class="fe-mini-row"><span>Até o Ens. Técnico</span><span class="fe-mini-val"><b id="resumo-tecnico"></b></span></div>
            <div class="fe-mini-row"><span>Até o Ens. Superior</span><span class="fe-mini-val"><b id="resumo-superior"></b></span></div>
            <div class="fe-mini-row"><span>Com Pós-Graduação</span><span class="fe-mini-val"><b id="resumo-pos"></b></span></div>
        </div>
    </div>
    <div class="fe-panel">
        <h4>Estado Civil</h4>
        <div class="fe-mini-list">
            <div class="fe-mini-row"><span>Solteiros</span><span class="fe-mini-val"><b id="resumo-solteiros"></b></span></div>
            <div class="fe-mini-row"><span>Casados</span><span class="fe-mini-val"><b id="resumo-casados"></b></span></div>
            <div class="fe-mini-row"><span>Divorciados</span><span class="fe-mini-val"><b id="resumo-divorciados"></b></span></div>
            <div class="fe-mini-row"><span>Viúvos</span><span class="fe-mini-val"><b id="resumo-viuvos"></b></span></div>
            <div class="fe-mini-row"><span>Sócio com Filhos</span><span class="fe-mini-val"><b id="resumo-filhos"></b></span></div>
        </div>
    </div>
    <div class="fe-panel">
        <h4>Deficiências</h4>
        <div class="fe-mini-list">
            <div class="fe-mini-row"><span>Surdos</span><span class="fe-mini-val"><b id="resumo-surdos"></b></span></div>
            <div class="fe-mini-row"><span>Deficiência Auditiva</span><span class="fe-mini-val"><b id="resumo-auditiva"></b></span></div>
            <div class="fe-mini-row"><span>Cegos</span><span class="fe-mini-val"><b id="resumo-cegos"></b></span></div>
            <div class="fe-mini-row"><span>Baixa Visão</span><span class="fe-mini-val"><b id="resumo-baixa_visao"></b></span></div>
            <div class="fe-mini-row"><span>Física/motora inferiores</span><span class="fe-mini-val"><b id="resumo-fisica_inferior"></b></span></div>
            <div class="fe-mini-row"><span>Física/motora superiores</span><span class="fe-mini-val"><b id="resumo-fisica_superior"></b></span></div>
            <div class="fe-mini-row"><span>Transtorno Neurológico</span><span class="fe-mini-val"><b id="resumo-neurologico"></b></span></div>
            <div class="fe-mini-row"><span>Deficiência Intelectual</span><span class="fe-mini-val"><b id="resumo-intelectual"></b></span></div>
        </div>
    </div>
</div>

<div class="fe-panels" style="grid-template-columns: repeat(1, minmax(0, 1fr)); max-width: 360px; margin-top: 18px;">
    <div class="fe-panel">
        <h4>Discipulado</h4>
        <div class="fe-mini-list">
            <div class="fe-mini-row"><span>Trilha da CNM</span><span class="fe-mini-val"><b id="resumo-trilha_cnm"></b></span></div>
            <div class="fe-mini-row"><span>Método da CNM</span><span class="fe-mini-val"><b id="resumo-discipulando_cnm"></b></span></div>
            <div class="fe-mini-row"><span>Outro método</span><span class="fe-mini-val"><b id="resumo-discipulando_outro"></b></span></div>
            <div class="fe-mini-row"><span>Sendo discipulados</span><span class="fe-mini-val"><b id="resumo-sendo_discipulados"></b></span></div>
        </div>
    </div>
</div>
