@extends('layouts.app')

@section('content')

@role('local')
@include('dashboard.index.local.index')
@endRole

@role('federacao')
@include('dashboard.index.federacao.index')
@endRole

@endsection