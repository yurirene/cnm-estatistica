@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => $pesquisa->nome
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
                    {!! Form::open(['method' => 'POST', 'route' => 'dashboard.pesquisas.responder', 'files' => true]) !!}
                    {!! Form::hidden('pesquisa_id', $pesquisa->id) !!}
                    <div id="formulario-renderizado"></div>
                    {!! Form::submit('Responder', ['class' => 'btn btn-success']) !!}
                    <a href="{{ route('dashboard.pesquisas.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('js')
<script>
    jQuery(function($) {
        //var formulario = document.getElementById('formulario-renderizado');
        $('#formulario-renderizado').formRender({
            dataType: 'json',
            formData: JSON.parse(@json($pesquisa->formulario))
        });
    });
</script>
@endpush