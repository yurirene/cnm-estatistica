@extends('layouts.app')

@section('content')


@include('dashboard.partes.head', [
    'titulo' => 'Comissão Executiva'
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
                                id="ce-delegados-tab"
                                data-toggle="pill"
                                href="#ce-delegados"
                                role="tab"
                                aria-controls="ce-delegados"
                                aria-selected="false">
                                Delegados
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
                        <div class="tab-pane fade {{ session()->get('aba') == 1 ? 'active show' : ''}}" id="ce-delegados" role="tabpanel" aria-labelledby="ce-delegados-tab">
                            <div class="">
                                <a href="{{ route('dashboard.comissao-executiva.sincronizar-inscritos', $reuniao->id) }}" class="btn btn-primary">Sincronizar Inscritos</a>
                            </div>
                            <div class="table-responsive">
                                {!! $delegadosDataTable->table(['style'=>'width:100%']) !!}
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
{!! $delegadosDataTable->scripts() !!}

@endpush
