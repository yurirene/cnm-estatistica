@extends('layouts.app-externo', [
    'export' => true
])

@section('content')
<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-12 text-center">
            <img class="img-responsive" src="/img/images.png" />
            <h1 class="text-center">Relatório Estatístico</h1>
            <h4 class="text-center">{{ auth()->user()->locais->first()->nome }}</h4>
        </div>
    </div>
    <div class="row mt-5" id="formulario_ump">
        <div class="col-xl-12 mb-5 mb-xl-0">
            {!! Form::model($formulario, ['route' => ['home']]) !!}
                    
                <h3>Perfil do Sócio</h3>
                @include('dashboard.formularios.local.perfil')

                <hr class="my-3">

                <h3>Estado Civil</h3>
                @include('dashboard.formularios.local.estado_civil')

                <hr class="my-3">

                <h3>Escolaridade</h3>
                @include('dashboard.formularios.local.escolaridade')

                <hr class="my-3">
                <div style="page-break-after: always;"></div>
                <h3>Deficiência</h3>
                @include('dashboard.formularios.local.deficiencia', [
                    'export' => true
                ])

                <hr class="my-3">

                <h3>Programações</h3>
                @include('dashboard.formularios.local.programacoes')

                <hr class="my-3">

                <h3>ACI</h3>
                @include('dashboard.formularios.local.aci')
            {!! Form::close() !!}
        </div>
    </div>
</div>  
<script>
    window.print()
</script>
@endsection
