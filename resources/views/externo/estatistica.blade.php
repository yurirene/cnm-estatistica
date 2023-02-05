@extends('layouts.app-externo', [
    'export' => true
])

@section('content')
    @include('dashboard.index.estatistica.cards')

    <div class="container-fluid mt--7">
        <div class="row mb-5">
            <div class="col-xl-12 mb-xl-0">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="text-uppercase text-light ls-1 mb-1">Parâmetros</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row d-flex align-items-center">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ano</label>
                                    <select class="form-control" id="ano">
                                        <option value="2022">2022</option>
                                    <select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Região</label>
                                    <select class="form-control" id="regiao">
                                        <option value="">Todas</option>
                                        <option value="1">Norte</option>
                                        <option value="2">Nordeste</option>
                                        <option value="3-oeste">Centro-Oeste</option>
                                        <option value="4">Sudeste</option>
                                        <option value="5">Sul</option>
                                    <select>
                                </div>
                            </div>
                            <div class="col-md-3 mt-1">
                                <button class="btn btn-default" typle="button" id="filtrar">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="mt-3 col-xl-3 col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Tipo de Sócios</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body ">
                        @include('dashboard.partes.skeleton')
                        <div class="table-responsive">
                            <canvas id="tipo_socios"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-xl-3 col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Gênero dos Sócios</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('dashboard.partes.skeleton')
                        <div class="table-responsive">
                            <canvas id="genero"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-xl-6 col-md-12">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Idade dos Sócios</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('dashboard.partes.skeleton')
                        <div class="table-responsive">
                            <canvas id="idade"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">

            <div class="mt-3 col-xl-6 col-md-12">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Estado Civil dos Sócios</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('dashboard.partes.skeleton')
                        <div class="table-responsive">
                            <canvas id="estado_civil"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-xl-3 col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Escolaridade dos Sócios</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('dashboard.partes.skeleton')
                        <div class="table-responsive">
                            <canvas id="escolaridade"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-xl-3 col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Sócios Desempregados</h6>
                            </div>
                        </div>
                    </div>
                        @include('dashboard.partes.skeleton')
                    <div class="card-body d-flex align-items-center">
                        <div class="table-responsive">
                            <canvas id="desempregados"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="mt-3 col-xl-8 col-md-12">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Sócios com Deficiências</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('dashboard.partes.skeleton')
                        <div class="table-responsive">
                            <canvas id="deficiencias"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-xl-4 col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Repasse da ACI</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('dashboard.partes.skeleton')
                        <div class="table-responsive">
                            <canvas id="repasse_aci"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="mt-3 col-xl-6 col-md-12">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Programações - Sinodais</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('dashboard.partes.skeleton')
                        <div class="table-responsive">
                            <canvas id="programacoes_sinodais"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 col-xl-6 col-md-12">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Programações - Federações</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('dashboard.partes.skeleton')
                        <div class="table-responsive">
                            <canvas id="programacoes_federacoes"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="mt-3 col-xl-6 col-md-12">
                <div class="card shadow h-100">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Programações - UMPs Locais</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('dashboard.partes.skeleton')
                        <div class="table-responsive">
                            <canvas id="programacoes_umps"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth')
    </div>
@endsection

@push('js')
@include('dashboard.index.estatistica.script')

@endpush
