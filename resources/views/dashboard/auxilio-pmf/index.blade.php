@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Auxílio PMF',
    'subtitulo' => $edital->titulo ?? null,
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-4">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Documentos do edital</h3>
                        </div>
                        @if($isAdmin)
                        <div class="col text-right">
                            <a href="{{ route('dashboard.auxilio-pmf.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Novo edital
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(!$edital)
                        <p class="text-muted mb-0">Nenhum edital cadastrado no momento.</p>
                    @else
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="{{ $edital->link_edital }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-file-alt"></i> Edital
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ $edital->link_proposta }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-file-signature"></i> Proposta
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ $edital->link_relatorio }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-file-invoice"></i> Relatório de prestação de contas
                                </a>
                            </div>
                        </div>
                        <p class="mb-0 text-sm">
                            <strong>Ano:</strong> {{ $edital->ano }}
                            &nbsp;|&nbsp;
                            <strong>Prazo da proposta:</strong> {{ $edital->data_limite_proposta->format('d/m/Y') }}
                            &nbsp;|&nbsp;
                            <strong>Recebimento:</strong>
                            @if($edital->estaRecebendo())
                                <span class="badge badge-success">Aberto</span>
                            @else
                                <span class="badge badge-danger">Encerrado</span>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($isAdmin)
        @include('dashboard.auxilio-pmf.admin')
    @elseif($isSinodal)
        @include('dashboard.auxilio-pmf.sinodal')
    @endif
</div>
@endsection

@if($isAdmin)
@push('js')
{!! $dataTable->scripts() !!}
@endpush
@endif
