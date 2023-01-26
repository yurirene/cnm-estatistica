@extends('layouts.app-externo', [
    'export' => true
])

@section('content')
<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-12 text-center">
            <img class="img-responsive" src="/img/images.png" />
            <h1 class="text-center">Relatório Estatístico</h1>
            <h4 class="text-center">{{ $formulario->local->nome }}</h4>
        </div>
    </div>

    <div class="row mt-5" id="formulario_ump">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="row">
                <div class="col-md-4 p-2">
                    <div class="card h-100 border">
                        <div class="card-header">
                            Perfil dos Sócios
                        </div>
                        <div class="card-body">
                            @include('dashboard.formularios.local.export.perfil')
                        </div>
                    </div>
                </div>
                <div class="col-md-4 p-2">
                    <div class="card h-100 border">
                        <div class="card-header">
                            Escolaridade dos Sócios
                        </div>
                        <div class="card-body">
                            @include('dashboard.formularios.local.export.escolaridade')
                        </div>
                    </div>
                </div>
                <div class="col-md-4 p-2">
                    <div class="card h-100 border">
                        <div class="card-header">
                            Estado Civil dos Sócios
                        </div>
                        <div class="card-body">
                            @include('dashboard.formularios.local.export.estado_civil')
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 p-2">
                    <div class="card h-100 border">
                        <div class="card-header">
                            Deficiência dos Sócios
                        </div>
                        <div class="card-body">
                            @include('dashboard.formularios.local.export.deficiencia')
                        </div>
                    </div>
                </div>
                <div class="col-md-4 p-2">
                    <div class="card h-100 border">
                        <div class="card-header">
                            Programações Realizadas
                        </div>
                        <div class="card-body">
                            @include('dashboard.formularios.local.export.programacoes')
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    window.print()
</script>
@endsection
