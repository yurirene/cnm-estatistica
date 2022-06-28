@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Comprovantes ACI'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        @if(auth()->user()->roles->first()->name == 'tesouraria')
            @include('dashboard.comprovante-aci.tesouraria')
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