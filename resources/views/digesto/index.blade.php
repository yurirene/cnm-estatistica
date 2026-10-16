@extends('layouts.app-externo', ['class' => 'bg-default'])

@section('content')
    <div class="header bg-gradient-primary digesto-hero">
        <div class="container">
            <h1 class="text-white mb-2">Digesto CNM</h1>
            <p class="digesto-hero-lead mb-0">
                Busca integral em relatórios, propostas e atas de reuniões da Confederação Nacional de Mocidade.
            </p>
        </div>
    </div>

    <div class="container digesto-busca pb-5">
        @include('digesto.partes.busca')

        @if($buscou)
            <div class="row">
                <div class="col-lg-3 mb-4 mb-lg-0">
                    @include('digesto.partes.facets')
                </div>
                <div class="col-lg-9">
                    @include('digesto.partes.resultados')
                </div>
            </div>
        @endif
    </div>
@endsection

@push('js')
    @include('digesto.partes.scripts')
@endpush
