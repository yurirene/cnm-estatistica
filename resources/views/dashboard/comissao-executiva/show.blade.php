@extends('layouts.app')

@section('content')


{{-- @include('dashboard.produtos.cards') --}}
@include('dashboard.partes.head', [
    'remover' => true,
    'titulo' => 'Produtos'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-pills" id="ce-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ !session()->has('aba') || session()->get('aba') == 0 ? 'active' : '' }}"
                                id="ce-documentos-tab"
                                data-toggle="pill"
                                href="#ce-documentos"
                                role="tab"
                                aria-controls="ce-documentos"
                                aria-selected="true">
                                Documentos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ session()->get('aba') == 1 ? 'active' : '' }}"
                                id="ce-credenciais-tab"
                                data-toggle="pill"
                                href="#ce-credenciais"
                                role="tab"
                                aria-controls="ce-credenciais"
                                aria-selected="false">
                                Credenciais
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="ce-tabContent">
                        <div class="tab-pane fade {{ !session()->has('aba') || session()->get('aba') == 0 ? 'active show' : ''}}" id="ce-documentos" role="tabpanel" aria-labelledby="ce-documentos-tab">
                            <div class="table-responsive">
                                {!! $dataTable->table(['style'=>'width:100%']) !!}
                            </div>
                        </div>
                        <div class="tab-pane fade {{ session()->get('aba') == 1 ? 'active show' : ''}}" id="ce-credenciais" role="tabpanel" aria-labelledby="ce-credenciais-tab">
                            <div class="table-responsive">
                                {!! $credenciaisDataTable->table(['style'=>'width:100%']) !!}
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

{!! $dataTable->scripts() !!}
{!! $credenciaisDataTable->scripts() !!}

@endpush
