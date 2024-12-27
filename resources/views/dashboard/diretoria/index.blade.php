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
                    {!! Form::model(
                        $diretoria,
                        [
                            'url' => route($route, ['diretoria' => $diretoria->id]),
                            'method' => 'PUT'
                        ]
                    ) !!}
                    <div class="row">
                        @foreach($cargos as $campo => $cargo)
                        <div class="col-lg-6 col-md-12">
                            <div class="card mb-3 border-0">
                                <div class="row g-0 d-flex align-items-center">
                                    <div class="col-md-12 text-center">
                                        <h5 class="">{{ $cargo }}</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::text($campo, null, [
                                                'class' => 'form-control float-right',
                                                'autocomplete' => 'off',
                                                'placeholder' => 'Nome'
                                            ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::text("contato_{$campo}", null, [
                                                'class' => 'form-control float-right',
                                                'autocomplete' => 'off',
                                                'placeholder' => 'Contato'
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-lg-6 col-md-12">
                            <div class="card mb-3 border-0">
                                <div class="row g-0 d-flex align-items-center">
                                    <div class="col-md-12 text-center">
                                        <h5 class="">Secretários</h5>
                                    </div>
                                    <div class="col-md-12">
                                        @foreach($secretarias as $chave => $secretaria)
                                            {!! Form::checkbox(
                                                'secretarios[]',
                                                $chave,
                                                null,
                                                []
                                            ) !!}
                                            {{ $secretaria }} <br>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button class="btn btn-success">
                                <i class='fas fa-save'></i>
                                Atualizar
                            </button>
                        </div>
                    </div>
                    {!! Form::close() !!}
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
