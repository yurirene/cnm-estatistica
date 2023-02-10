@extends('layouts.app')

@section('content')


@include('dashboard.produtos.cards')
@include('dashboard.partes.head', [
    'remover' => true,
    'titulo' => 'Produtos'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-pills" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ session()->get('aba') == 0 ? 'active' : '' }}"
                                id="custom-tabs-four-home-tab"
                                data-toggle="pill"
                                href="#custom-tabs-four-home"
                                role="tab"
                                aria-controls="custom-tabs-four-home"
                                aria-selected="true">
                                Produtos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ session()->get('aba') == 1 ? 'active' : '' }}"
                                id="custom-tabs-four-profile-tab"
                                data-toggle="pill"
                                href="#custom-tabs-four-profile"
                                role="tab"
                                aria-controls="custom-tabs-four-profile"
                                aria-selected="false">
                                Estoque
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ session()->get('aba') == 2 ? 'active' : '' }}"
                                id="custom-tabs-five-profile-tab"
                                data-toggle="pill"
                                href="#custom-tabs-five-profile"
                                role="tab"
                                aria-controls="custom-tabs-five-profile"
                                aria-selected="false">
                                Consignação
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ session()->get('aba') == 3 ? 'active' : '' }}"
                                id="custom-tabs-fluxo-tab"
                                data-toggle="pill"
                                href="#custom-tabs-fluxo"
                                role="tab"
                                aria-controls="custom-tabs-fluxo"
                                aria-selected="false">
                                Fluxo Caixa
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <div class="tab-pane fade  {{ session()->get('aba') == 0 ? 'show active' : '' }}" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                            <div class="table-responsive">
                                {!! $produtosDataTable->table(['style'=>'width:100%']) !!}
                            </div>
                        </div>
                        <div class="tab-pane fade {{ session()->get('aba') == 1 ? 'show active' : '' }}" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">
                            <div class="table-responsive">
                                {!! $estoqueProdutosDataTable->table(['style'=>'width:100%']) !!}
                            </div>
                        </div>
                        <div class="tab-pane fade {{ session()->get('aba') == 2 ? 'show active' : '' }}" id="custom-tabs-five-profile" role="tabpanel" aria-labelledby="custom-tabs-five-profile-tab">
                            <div class="table-responsive">
                                {!! $consignacaoProdutosDataTable->table(['style'=>'width:100%']) !!}
                            </div>
                        </div>
                        <div class="tab-pane fade {{ session()->get('aba') == 3 ? 'show active' : '' }}" id="custom-tabs-fluxo" role="tabpanel" aria-labelledby="custom-tabs-fluxo-tab">
                            <div class="table-responsive">
                                {!! $fluxoCaixaDataTable->table(['style'=>'width:100%']) !!}
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

{!! $produtosDataTable->scripts() !!}
{!! $estoqueProdutosDataTable->scripts() !!}
{!! $consignacaoProdutosDataTable->scripts() !!}
{!! $fluxoCaixaDataTable->scripts() !!}

@endpush
