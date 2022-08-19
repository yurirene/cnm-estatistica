@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Estatística'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Painel de Estatística</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist" style="line-height: 40px;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" 
                                id="primeiro-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#primeiro" 
                                type="button" 
                                role="tab" 
                                aria-controls="primeiro" 
                                aria-selected="true">Configurações
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" 
                                id="segundo-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#segundo" 
                                type="button" 
                                role="tab" 
                                aria-controls="segundo" 
                                aria-selected="false">Relatórios
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="primeiro" role="tabpanel" aria-labelledby="primeiro-tab">
                            <div class="row mt-3">
                                <div class="col-md-12 mt-3">
                                    <div class="card">

                                        {!! Form::open(['method' => 'POST', 'route' => 'dashboard.estatistica.atualizarParametro']) !!}
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col">
                                                    <h2>Parâmetros</h2>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @foreach($parametros as $parametro)
                                                <div class="col-md-3">
    
                                                    <span>{{ $parametro['label'] }}</span><br>
                                                    <input type="checkbox" 
                                                        data-toggle="toggle" 
                                                        data-onstyle="success" 
                                                        data-on="Ativado" 
                                                        data-off="Desativado" 
                                                        name="{{ $parametro['nome']}}" 
                                                        id="{{ $parametro['nome']}}" 
                                                        {{$parametro['valor'] == 'SIM' ? 'checked' : ''}} >
                                                </div>
                                                @endforeach
                                            </div>
                                            <div class="row mt-5">
                                                <div class="col">
                                                    <button class="btn btn-success" type="submit">Salvar</button>
                                                </div>
                                            </div>
                                        </div>

                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="segundo" role="tabpanel" aria-labelledby="segundo-tab">
                            <div class="row mt-3">
                                <div class="col-md-12 mt-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive"> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>    
                        </div>
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