@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Game CNM',
    'subtitulo' => 'Pontuação do Planejamento Estratégico',
    'game' => $game,
])

<div class="container-fluid mt--7 pb-5">
    @include('dashboard.index.gamificacao.hero', ['game' => $game])
    @include('dashboard.index.gamificacao.pilares', ['game' => $game])
</div>

@endsection
