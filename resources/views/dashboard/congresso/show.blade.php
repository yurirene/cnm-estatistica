@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Detalhes do Congresso'
])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ $reuniao->tipo }} - {{ $reuniao->ano }}</h3>
                            <p class="text-muted mb-0">{{ $reuniao->local }}</p>
                        </div>
                        <div class="col text-right">
                            <a href="{{ route('dashboard.congresso.delegado.create', $reuniao->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Adicionar Delegado
                            </a>
                            <a href="{{ route('dashboard.congresso.edit', $reuniao->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="{{ route('dashboard.congresso.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informações do Congresso</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Tipo:</strong></td>
                                    <td>{{ $reuniao->tipo }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ano:</strong></td>
                                    <td>{{ $reuniao->ano }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Local:</strong></td>
                                    <td>{{ $reuniao->local }}</td>
                                </tr>
                                @if($reuniao->sinodal)
                                <tr>
                                    <td><strong>Sinodal:</strong></td>
                                    <td>{{ $reuniao->sinodal->nome }}</td>
                                </tr>
                                @endif
                                @if($reuniao->federacao)
                                <tr>
                                    <td><strong>Federação:</strong></td>
                                    <td>{{ $reuniao->federacao->nome }}</td>
                                </tr>
                                @endif
                                @if($reuniao->local)
                                <tr>
                                    <td><strong>Local:</strong></td>
                                    <td>{{ $reuniao->local->nome }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Configurações</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($reuniao->status == 1)
                                            <span class="badge badge-success">Ativo</span>
                                        @elseif($reuniao->status == 0)
                                            <span class="badge badge-secondary">Inativo</span>
                                        @else
                                            <span class="badge badge-danger">Encerrado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Documentos:</strong></td>
                                    <td>
                                        @if($reuniao->aberto)
                                            <span class="badge badge-success">Abertos</span>
                                        @else
                                            <span class="badge badge-warning">Fechados</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Diretoria:</strong></td>
                                    <td>
                                        @if($reuniao->diretoria)
                                            <span class="badge badge-info">Requerida</span>
                                        @else
                                            <span class="badge badge-secondary">Não requerida</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Relatório Estatístico:</strong></td>
                                    <td>
                                        @if($reuniao->relatorio_estatistico)
                                            <span class="badge badge-info">Requerido</span>
                                        @else
                                            <span class="badge badge-secondary">Não requerido</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($reuniao->descricao)
                    <div class="row mb-4">
                        <div class="col">
                            <h5>Descrição</h5>
                            <p>{{ $reuniao->descricao }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col">
                            <h5>Delegados</h5>
                            {!! $dataTable->table(['class' => 'table table-striped table-bordered']) !!}
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
<script>
$(document).ready(function() {
    // Scripts específicos da visualização do congresso
});
</script>
@endpush
