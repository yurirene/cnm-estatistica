@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Pesquisas',
])
    
<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Formulário</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="fb-editor"></div>
                    <div id="fb-rendered-form" style="display: none;">
                        {!! Form::open(['method' => 'POST', 'route' => 'dashboard.pesquisas.store']) !!}
                        {!! Form::hidden('formulario') !!}
                        <button class="btn btn-default edit-form">Edit</button>
                        <button class="btn btn-success" type="submit">Enviar</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection
@push('js')

@include('dashboard.pesquisas.js.options')
<script>
    jQuery(function($) {
        formBuilder = $(document.getElementById('fb-editor')).formBuilder(options);
    });
</script>
@endpush