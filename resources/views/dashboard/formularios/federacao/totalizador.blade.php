<div class="row">
    <div class="col-md-12">
        <div class="progress-wrapper">
            <div class="progress-info">
                <div class="progress-percentage">
                    <span class="text-sm font-weight-bold">
                        {{$qualidade_entrega['porcentagem']}}% dos Formulários Entregues (Ano Referência - {{ $ano_referencia }})</span> -
                        <small>{{$qualidade_entrega['texto']}}</small>
                </div>
            </div>
            <div class="progress" style="height: 15px;">
                <div
                    class="progress-bar bg-{{$qualidade_entrega['color']}}"
                    role="progressbar"
                    aria-valuenow="{{$qualidade_entrega['porcentagem']}}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    style="width: {{$qualidade_entrega['porcentagem']}}%;">
                </div>
                {{$qualidade_entrega['porcentagem']}}%
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12">
        ACI Recebida: R$<b id="aci-recebida"></b><br>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <p>
            <h4>Perfil</h4>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Ativos
                    <span class="badge badge-primary badge-pill"><b id="resumo-ativos"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Cooperadores
                    <span class="badge badge-primary badge-pill"><b id="resumo-cooperadores"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Homens
                    <span class="badge badge-primary badge-pill"><b id="resumo-mulheres"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Mulheres
                    <span class="badge badge-primary badge-pill"><b id="resumo-homens"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Menor de 19
                    <span class="badge badge-primary badge-pill"><b id="resumo-menor19"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    19 a 23
                    <span class="badge badge-primary badge-pill"><b id="resumo-de19a23"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    24 a 29
                    <span class="badge badge-primary badge-pill"><b id="resumo-de24a29"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    30 a 35
                    <span class="badge badge-primary badge-pill"><b id="resumo-de30a35"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Desempregados
                    <span class="badge badge-primary badge-pill"><b id="resumo-desempregado"></b></span>
                </li>
            </ul>
        </p>
    </div>
    <div class="col-md-3">
        <p>
            <h4>Escolaridade</h4>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Até o Ens. Fundamental
                    <span class="badge badge-primary badge-pill"><b id="resumo-fundamental"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Até o Ens. Médio
                    <span class="badge badge-primary badge-pill"><b id="resumo-medio"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Até o Ens. Técnino
                    <span class="badge badge-primary badge-pill"><b id="resumo-tecnico"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Até o Ens. Superior
                    <span class="badge badge-primary badge-pill"><b id="resumo-superior"></b></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Com Pós-Graduação
                    <span class="badge badge-primary badge-pill"><b id="resumo-pos"></b></span>
                </li>
            </ul>
        </p>
    </div>
    <div class="col-md-3">
        <p>
            <h4>Estado Civil</h4>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Solteiros
                    <span class="badge badge-primary badge-pill"><b id="resumo-solteiros"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Casados
                    <span class="badge badge-primary badge-pill"><b id="resumo-casados"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Divorciados
                    <span class="badge badge-primary badge-pill"><b id="resumo-divorciados"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Viúvos
                    <span class="badge badge-primary badge-pill"><b id="resumo-viuvos"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Sócio com Filhos
                    <span class="badge badge-primary badge-pill"><b id="resumo-filhos"></b></span>
                </li>
            </ul>
        </p>
    </div>
    <div class="col-md-3">
        <p>
            <h4>Deficiências</h4>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Surdos
                    <span class="badge badge-primary badge-pill"><b id="resumo-surdos"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Deficiência Auditiva
                    <span class="badge badge-primary badge-pill"><b id="resumo-auditiva"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Cegos
                    <span class="badge badge-primary badge-pill"><b id="resumo-cegos"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Baixa Visão
                    <span class="badge badge-primary badge-pill"><b id="resumo-baixa_visao"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Deficiência física/motora em membros inferiores
                    <span class="badge badge-primary badge-pill"><b id="resumo-fisica_inferior"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Deficiência física/motora em membros superiores
                    <span class="badge badge-primary badge-pill"><b id="resumo-fisica_superior"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Transtorno Neurológico
                    <span class="badge badge-primary badge-pill"><b id="resumo-neurologico"></b></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Deficiência Intelectual
                    <span class="badge badge-primary badge-pill"><b id="resumo-intelectual"></b></span>
                </li>
            </ul>
        </p>
    </div>
</div>
