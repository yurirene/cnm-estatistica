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
                    {!! Form::open(['method' => 'POST', 'route' => 'dashboard.pesquisas.store']) !!}
                    <div class="form-group{{ $errors->has('nome') ? ' has-error' : '' }}">
                        {!! Form::label('nome', 'Titulo do Formulário') !!}
                        {!! Form::text('nome', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        <small class="text-danger">{{ $errors->first('nome') }}</small>
                    </div>
                    <div id="fb-editor"></div>
                    <div id="fb-rendered-form" style="display: none;">
                        
                        {!! Form::hidden('formulario') !!}
                        <button class="btn btn-default edit-form">Edit</button>
                        <button class="btn btn-success" type="submit">Enviar</button>
                    </div>
                    {!! Form::close() !!}
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