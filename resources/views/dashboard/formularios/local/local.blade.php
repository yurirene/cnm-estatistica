@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Formulários - UMP Local'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
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
                    {!! Form::open(['method' => 'POST', 'route' => 'dashboard.formularios-locais.store', 'class' => 'form-horizontal']) !!}
                    <h3>Perfil do Sócio</h3>
                    @include('dashboard.formularios.local.perfil')

                    <hr class="my-3">

                    <h3>Estado Civil</h3>
                    @include('dashboard.formularios.local.estado_civil')

                    <hr class="my-3">

                    <h3>Escolaridade</h3>
                    @include('dashboard.formularios.local.escolaridade')

                    <hr class="my-3">

                    <h3>Deficiência</h3>
                    @include('dashboard.formularios.local.deficiencia')

                    <hr class="my-3">

                    <h3>Programações</h3>
                    @include('dashboard.formularios.local.programacoes')

                    <hr class="my-3">

                    <h3>ACI</h3>
                    @include('dashboard.formularios.local.aci')


                    <div class="btn-group pull-right">
                    {!! Form::submit('Enviar', ['class' => 'btn btn-success']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>  
@endsection

@push('js')
<script>

$(document).ready(function() {

});

</script>
@endpush