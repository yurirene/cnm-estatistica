@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Comissão Executiva'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        @if(auth()->user()->roles->first()->name == 'sinodal')
            @include('dashboard.comissao-executiva.sinodal')
        @else
            @include('dashboard.comprovante-aci.sinodal')
        @endif
    </div>
</div>
@endsection

@push('js')
{!! $dataTable->scripts() !!}
<script>

$(document).ready(function() {

});

</script>
@endpush
