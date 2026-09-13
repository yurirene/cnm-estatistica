@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Comprovantes ACI',
    'url_tutorial' => config('tutoriais.comprovante-aci.index')
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        @if(auth()->user()->role->name == 'tesouraria')
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
    window.initComprovanteAciTooltips = function () {
        $('#comprovantes-table [data-toggle="tooltip"]').each(function () {
            var $el = $(this);
            if (typeof $el.tooltip !== 'function') {
                return;
            }
            if ($el.data('bs.tooltip')) {
                $el.tooltip('dispose');
            }
            $el.tooltip({
                container: 'body',
                trigger: 'hover focus',
                placement: 'top'
            });
        });
    };

    $(document).on('draw.dt', '#comprovantes-table', function () {
        window.initComprovanteAciTooltips();
    });
</script>
@endpush