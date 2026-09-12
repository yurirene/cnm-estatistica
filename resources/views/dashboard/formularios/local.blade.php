@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Formulário Estatístico',
    'url_tutorial' => config('tutoriais.estatistica.local')
])

@php
    $temComplementar = (!is_null($formularioComplementarSinodal) && $formularioComplementarSinodal->formulario != null)
        || (!is_null($formularioComplementarFederacao) && $formularioComplementarFederacao->formulario != null);
    $feSteps = [
        ['title' => 'Perfil do Sócio', 'desc' => 'Quantidade de sócios por tipo, faixa etária e gênero.'],
        ['title' => 'Estado Civil', 'desc' => 'Situação civil dos sócios ativos e cooperadores.'],
        ['title' => 'Escolaridade', 'desc' => 'Nível de ensino concluído pelos sócios.'],
        ['title' => 'Deficiência', 'desc' => 'Toque no ícone “i” para ver a definição de cada categoria.'],
        ['title' => 'Programações', 'desc' => 'Atividades realizadas pela UMP ao longo do ano.'],
        ['title' => 'Discipulado', 'desc' => 'Os dois primeiros campos são calculados automaticamente a partir do Formulário Complementar.'],
        ['title' => 'Organização', 'desc' => 'Participação em treinamentos oferecidos pelas instâncias superiores.'],
        ['title' => 'ACI', 'desc' => 'Confirmação do repasse anual da contribuição para a Federação.'],
    ];
    if ($temComplementar) {
        $feSteps[] = ['title' => 'Complementar', 'desc' => 'Perguntas adicionais definidas pela Federação ou Sinodal.'];
    }
    $feStartStep = 0;
    if ($errors->any()) {
        $errorKeys = $errors->keys();
        $stepPrefixes = [
            0 => ['somatorio', 'perfil'],
            1 => ['estado_civil'],
            2 => ['escolaridade'],
            3 => ['deficiencias'],
            4 => ['programacoes'],
            5 => ['discipulado'],
            6 => ['organizacao'],
            7 => ['aci'],
        ];
        foreach ($stepPrefixes as $step => $prefixes) {
            foreach ($errorKeys as $key) {
                foreach ($prefixes as $prefix) {
                    if ($key === $prefix || str_starts_with((string) $key, $prefix . '[') || str_starts_with((string) $key, $prefix . '.')) {
                        $feStartStep = $step;
                        break 3;
                    }
                }
            }
        }
    }
@endphp

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Formulário Estatístico - UMP Local</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-inline">
                                @if(count($anos) > 0)
                                <div class="form-group mb-2">
                                    {!! Form::label('Ano') !!}
                                    {!! Form::select(
                                        'ano',
                                        $anos,
                                        null,
                                        ['class' => 'form-control ml-1', 'id' => 'ano']
                                    ) !!}
                                </div>
                                <button type="button" id="visualizar" class="btn btn-primary mb-2 ml-3">
                                    Visualizar
                                </button>
                                <a href="#" id="link_export" target="_blank" class="btn btn-primary mb-2 ml-1">
                                    Exportar
                                </a>
                                @endif
                                @if($coleta)
                                    <button type="button" id="responder" class="btn btn-primary mb-2 ml-1">
                                        <span>Responder</span>
                                        <span class="badge bg-danger blob">{{$ano_referencia}}</span>
                                    </button>
                                    <a href="{{ route('dashboard.coletor-dados.index') }}" id="coleta-dados" class="btn btn-primary mb-2 ml-1">
                                        <span class="text-white">Coletor de Dados</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('dashboard.formularios.local.respostas')
    @if($coleta)
    <div class="row mt-5 fe-ui" id="formulario_ump" style="{{ $errors->any() ? ' ' : 'display: none;' }}">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Formulário Estatístico</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="fe-year-row">
                        <label>Ano referência</label>
                        <input type="text" class="fe-control form-control" value="{{ $ano_referencia }}" disabled />
                    </div>
                    <div class="fe-divider"></div>
                    @error('somatorio')
                        <div class="fe-alert">{{ $message }}</div>
                    @enderror

                    @if(!is_null($formulario))
                    {!! Form::model(
                        $formulario,
                        [
                            'route' => ['dashboard.formularios-locais.store'],
                            'method' => 'POST',
                            'class' => 'form-horizontal',
                            'novalidate' => true,
                            'id' => 'formulario-estatistico-form'
                        ]
                    ) !!}
                    @else
                    {!! Form::open(
                        [
                            'method' => 'POST',
                            'route' => 'dashboard.formularios-locais.store',
                            'class' => 'form-horizontal',
                            'novalidate' => true,
                            'id' => 'formulario-estatistico-form'
                        ]
                    ) !!}
                    @endif

                    <div class="fe-progress-label">Etapa <span id="fe-step-num">1</span> de <span id="fe-step-total">{{ count($feSteps) }}</span></div>
                    <div class="fe-progress-bar"><div class="fe-progress-fill" id="fe-progress-fill"></div></div>

                    <div class="fe-wizard" data-fe-start="{{ $feStartStep }}">
                        <div class="fe-wizard-steps" id="fe-wizard-steps">
                            @foreach($feSteps as $index => $step)
                                <button type="button" class="fe-wstep" data-fe-step-index="{{ $index }}">
                                    <span class="fe-num">{{ $index + 1 }}</span>
                                    {{ $step['title'] }}
                                </button>
                            @endforeach
                        </div>

                        <div class="fe-wizard-body">
                            <div class="fe-step-panel" data-step="0">
                                <h3 class="fe-step-title">{{ $feSteps[0]['title'] }}</h3>
                                <p class="fe-step-desc">{{ $feSteps[0]['desc'] }}</p>
                                @include('dashboard.formularios.local.perfil')
                            </div>
                            <div class="fe-step-panel" data-step="1">
                                <h3 class="fe-step-title">{{ $feSteps[1]['title'] }}</h3>
                                <p class="fe-step-desc">{{ $feSteps[1]['desc'] }}</p>
                                @include('dashboard.formularios.local.estado_civil')
                            </div>
                            <div class="fe-step-panel" data-step="2">
                                <h3 class="fe-step-title">{{ $feSteps[2]['title'] }}</h3>
                                <p class="fe-step-desc">{{ $feSteps[2]['desc'] }}</p>
                                @include('dashboard.formularios.local.escolaridade')
                            </div>
                            <div class="fe-step-panel" data-step="3">
                                <h3 class="fe-step-title">{{ $feSteps[3]['title'] }}</h3>
                                <p class="fe-step-desc">{{ $feSteps[3]['desc'] }}</p>
                                @include('dashboard.formularios.local.deficiencia')
                            </div>
                            <div class="fe-step-panel" data-step="4">
                                <h3 class="fe-step-title">{{ $feSteps[4]['title'] }}</h3>
                                <p class="fe-step-desc">{{ $feSteps[4]['desc'] }}</p>
                                @include('dashboard.formularios.local.programacoes')
                            </div>
                            <div class="fe-step-panel" data-step="5">
                                <h3 class="fe-step-title">{{ $feSteps[5]['title'] }}</h3>
                                <p class="fe-step-desc">{{ $feSteps[5]['desc'] }}</p>
                                @include('dashboard.formularios.local.discipulado')
                            </div>
                            <div class="fe-step-panel" data-step="6">
                                <h3 class="fe-step-title">{{ $feSteps[6]['title'] }}</h3>
                                <p class="fe-step-desc">{{ $feSteps[6]['desc'] }}</p>
                                @include('dashboard.formularios.local.organizacao')
                            </div>
                            <div class="fe-step-panel" data-step="7">
                                <h3 class="fe-step-title">{{ $feSteps[7]['title'] }}</h3>
                                <p class="fe-step-desc">{{ $feSteps[7]['desc'] }}</p>
                                @include('dashboard.formularios.local.aci')
                                @if(!$temComplementar)
                                <x-formulario.consistencia
                                    id="fe-consist-revisao"
                                    class="is-info"
                                    text="Revise as etapas antes de enviar. Você pode voltar clicando em qualquer etapa na lista à esquerda."
                                />
                                @endif
                            </div>
                            @if($temComplementar)
                            <div class="fe-step-panel" data-step="8">
                                <h3 class="fe-step-title">{{ $feSteps[8]['title'] }}</h3>
                                <p class="fe-step-desc">{{ $feSteps[8]['desc'] }}</p>
                                @include('dashboard.formularios.local.complementar')
                                <x-formulario.consistencia
                                    id="fe-consist-revisao"
                                    class="is-info"
                                    text="Revise as etapas antes de enviar. Você pode voltar clicando em qualquer etapa na lista à esquerda."
                                />
                            </div>
                            @else
                                {{-- Mantém o include para o @push('js') do complementar não quebrar quando vazio --}}
                                @include('dashboard.formularios.local.complementar')
                            @endif

                            {!! Form::hidden(
                                'local_id',
                                auth()->user()->local_id,
                                ['class' => 'form-control','required'=>true, 'autocomplete' => 'off']
                            ) !!}

                            <div class="fe-wizard-nav">
                                <button type="button" class="fe-btn fe-btn-outline" id="fe-prev">Voltar</button>
                                <div class="fe-nav-right">
                                    <button type="button" class="fe-btn fe-btn-accent" id="fe-next">Próxima etapa</button>
                                    {!! Form::submit('Enviar formulário', ['class' => 'fe-btn fe-btn-accent fe-hidden', 'id' => 'fe-submit']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('css/formulario-estatistico.css') }}">
@endpush

@push('js')

@include('dashboard.formularios._js.formulario')
@include('dashboard.formularios._js.resumo')
@include('dashboard.formularios.local.js.script')

@endpush
