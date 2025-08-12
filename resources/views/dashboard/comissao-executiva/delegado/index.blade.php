@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Delegados - Comissão Executiva'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Editar Delegado</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('dashboard.comissao-executiva.delegado.form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Máscara para CPF
    $('.cpf').mask('000.000.000-00');
});
</script>
@endpush