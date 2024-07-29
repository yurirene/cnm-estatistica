@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Comissão Executiva'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        @if($isSinodal)
            @include('dashboard.comissao-executiva.sinodal')
        @else
            @include('dashboard.comissao-executiva.executiva')
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
