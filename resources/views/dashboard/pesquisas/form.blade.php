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
                    @if(isset($pesquisa))
                    {!! Form::model($pesquisa, ['route' => ['dashboard.pesquisas.update', $pesquisa->id], 'method' => 'PUT']) !!}
                    @else
                    {!! Form::open(['method' => 'POST', 'route' => 'dashboard.pesquisas.store']) !!}
                    @endif
                    <div class="form-group{{ $errors->has('nome') ? ' has-error' : '' }}">
                        {!! Form::label('nome', 'Titulo do Formulário') !!}
                        {!! Form::text('nome', null, ['class' => 'form-control', 'required' => 'required']) !!}
                        <small class="text-danger">{{ $errors->first('nome') }}</small>
                    </div>
                    <div class="form-group{{ $errors->has('secretarios') ? ' has-error' : '' }}">
                        {!! Form::label('secretarios[]', 'Secretários com acesso ao formulário') !!}
                        {!! Form::select('secretarios[]', $secretarios, isset($pesquisa) ? $pesquisa->usuarios->pluck('id') : null, ['class' => 'form-control isSelect2', 'required' => 'required', 'multiple' => 'true']) !!}
                        <small class="text-danger">{{ $errors->first('secretarios') }}</small>
                    </div>
                    <div class="form-group{{ $errors->has('instancias') ? ' has-error' : '' }}">
                        {!! Form::label('instancias[]', 'Instâncias') !!}
                        {!! Form::select('instancias[]', $instancias, isset($pesquisa) ? $pesquisa->instancias : null, ['class' => 'form-control isSelect2', 'required' => 'required', 'multiple' => 'true']) !!}
                        <small class="text-danger">{{ $errors->first('instancias') }}</small>
                    </div>
                    <h2>Formulário da Pesquisa</h2>

                    <div id="fb-editor" class="mt-5"></div>
                    <div id="fb-rendered-form" style="display: none;">
                        
                        {!! Form::hidden('formulario') !!}
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

@if(isset($pesquisa))
<script>
    $(document).ready(function() {
        var formDataEdit = JSON.parse(@json($pesquisa->formulario));
        formBuilder = $(document.getElementById('fb-editor')).formBuilder(options).promise.then(formBuilder => {
            formBuilder.actions.setData(formDataEdit);// after the builder loads, do you stuff here
        });
        
    })
</script>
@else
<script>
    $(function($) {
        formBuilder = $(document.getElementById('fb-editor')).formBuilder(options);
    });
</script>
@endif
@endpush