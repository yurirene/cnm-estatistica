@extends('layouts.app')

@section('content')

@can('permitido', ['local'])
@include('dashboard.index.local.index')
@endcan

@can('permitido', ['federacao'])
@include('dashboard.index.federacao.index')
@endcan

@can('permitido', ['sinodal'])
@include('dashboard.index.sinodal.index')
@endcan

@can('permitido', ['diretoria'])
@include('dashboard.index.diretoria.index')
@endcan

@can('permitido', ['tesouraria'])
@include('dashboard.index.tesouraria.index')
@endcan

@can('permitido', ['administrador'])
@include('dashboard.index.admin.index')
@endcan

@can('permitido', ['executiva'])
@include('dashboard.index.executiva.index')
@endcan

@can('permitido', ['secretaria_estatistica'])
@include('dashboard.index.estatistica.index')
@endcan

@can('permitido', ['secreatria_produtos'])
@include('dashboard.index.produtos.index')
@endcan

@endsection
