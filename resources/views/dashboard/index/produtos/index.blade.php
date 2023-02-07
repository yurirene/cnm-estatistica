@extends('layouts.app')

@section('content')

    @include('dashboard.partes.head', [
        'titulo' => 'Secretaria de Produtos'
    ])
    <div class="container mt-3">
        <div class="row">
            <div class="col-xl-12 mb-5 mb-xl-0">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-light ls-1 mb-1">Fluxo de Caixa</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="">
                            <canvas id="grafico_saldo"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    const grafico = @json(DashboardHelper::getTotalizadores());
    const acessoChart = new Chart(
        document.getElementById('grafico_saldo'),
        grafico
    );

</script>
@endpush
