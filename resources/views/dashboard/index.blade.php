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


@endsection