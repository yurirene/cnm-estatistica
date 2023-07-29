@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
'titulo' => 'Diretoria ' . $tipo
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Diretoria {{$tipo}}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($cargos as $cargo)
                        <div class="col-lg-6 col-md-12">
                            <div class="card mb-3 border-0">
                                <div class="row g-0 d-flex align-items-center">
                                    <div class="col-md-4 text-center">
                                        <img src="https://picsum.photos/200"
                                            class="img-fluid rounded-circle" style="min-width:100px;" alt="..."
                                        >
                                        <h5 class="">{{ $cargo['cargo'] }}</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <div class="input-group input-group-sm">
                                                {!! Form::text('nome_' . $cargo['key'], $cargo['nome'], [
                                                    'class' => 'form-control float-right',
                                                    'required'=>true,
                                                    'autocomplete' => 'off',
                                                    'placeholder' => 'Nome'
                                                ]) !!}
                                                <div class="input-group-append">
                                                    <button class="btn btn-success"><i class='fas fa-save'></i></button>
                                                </div>
                                            </div>
                                            <div class="input-group input-group-sm mt-3">
                                                {!! Form::text('contato_' . $cargo['key'], $cargo['contato'], [
                                                    'class' => 'form-control float-right',
                                                    'required'=>true,
                                                    'autocomplete' => 'off',
                                                    'placeholder' => 'Contato'
                                                ]) !!}
                                                <div class="input-group-append">
                                                    <button class="btn btn-success"><i class='fas fa-save'></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>

</script>
@endpush
