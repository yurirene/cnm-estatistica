@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Categoria',
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Editar Categoria</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::model(
                        $categoria,
                        [
                            'url' => route('dashboard.apps.tesouraria.categoria.update', $categoria->id),
                            'method' => 'PUT',
                            'files' => false
                        ]
                    ) !!}
                    <div class="row">

                        <div class="col-md-8">
                            <div class="form-group">
                                {!! Form::label('nome', 'Nome') !!}
                                {!! Form::text('nome', null, ['class' => 'form-control', 'required' => true, 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-group">
                                <button class="btn btn-primary">
                                    <em class="fas fa-save"></em>
                                    Salvar
                                </button>
                            </div>
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
