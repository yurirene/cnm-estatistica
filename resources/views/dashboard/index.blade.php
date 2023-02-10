@extends('layouts.app')

@section('content')

@role('local')
@include('dashboard.index.local.index')
@endRole

@role('federacao')
@include('dashboard.index.federacao.index')
@endRole

@role('sinodal')
@include('dashboard.index.sinodal.index')
@endRole

@role('diretoria')
@include('dashboard.index.diretoria.index')
@endRole

@role('tesouraria')
@include('dashboard.index.tesouraria.index')
@endRole

@role('administrador')
@include('dashboard.index.admin.index')
@endRole

@role('executiva')
@include('dashboard.index.executiva.index')
@endRole

@role('secretaria_estatistica')
@include('dashboard.index.estatistica.index')
@endRole

@role('secreatria_produtos')
@include('dashboard.index.produtos.index')
@endRole

@endsection
