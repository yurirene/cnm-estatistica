@props([
    'prefix' => '',
])

@php
    $id = $prefix !== '' ? $prefix . '-' : '';
@endphp

<div class="fe-grid-2">
    <div class="fe-panel">
        <h4>Perfil</h4>
        <div class="fe-chart-wrap"><canvas id="chart-{{ $id }}genero"></canvas></div>
    </div>
    <div class="fe-panel">
        <h4>Tipo de sócio</h4>
        <div class="fe-chart-wrap"><canvas id="chart-{{ $id }}tipo"></canvas></div>
    </div>
</div>

<div class="fe-grid-2">
    <div class="fe-panel">
        <h4>Escolaridade</h4>
        <div class="fe-chart-wrap"><canvas id="chart-{{ $id }}escolaridade"></canvas></div>
    </div>
    <div class="fe-panel">
        <h4>Faixa etária</h4>
        <div class="fe-chart-wrap"><canvas id="chart-{{ $id }}faixa"></canvas></div>
    </div>
</div>

<div class="fe-grid-2">
    <div class="fe-panel">
        <h4>Programações</h4>
        <div class="fe-chart-wrap"><canvas id="chart-{{ $id }}programacoes"></canvas></div>
    </div>
    <div class="fe-panel">
        <h4>Discipulado</h4>
        <div class="fe-chart-wrap"><canvas id="chart-{{ $id }}discipulado"></canvas></div>
    </div>
</div>
