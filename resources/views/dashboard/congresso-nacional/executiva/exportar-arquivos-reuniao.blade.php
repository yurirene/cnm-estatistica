@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Exportar arquivos da reunião por região'
])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Download dos arquivos (ZIP) por região</h3>
                </div>
                <div class="card-body">
                    @if($reuniao ?? null)
                        <p class="text-muted mb-4">Reunião: <strong>{{ $reuniao->nome }}</strong></p>
                    @else
                        <p class="text-muted mb-4">Arquivos sem reunião vinculada.</p>
                    @endif

                    @if($regioes->isEmpty())
                        <p class="mb-0">Nenhuma região com credenciais ou documentos recebidos no momento.</p>
                    @else
                        <p class="mb-4">Clique na região para baixar o ZIP com credenciais e documentos recebidos:</p>
                        <ul class="list-group list-group-flush">
                            @foreach($regioes as $regiao)
                                <li class="list-group-item d-flex align-items-center">
                                    <a href="{{ route('dashboard.cn.executiva.exportar-arquivos-reuniao-zip', $regiao) }}" class="text-decoration-none flex-grow-1">
                                        <i class="fas fa-file-archive text-primary me-2"></i>
                                        {{ $regiao->nome }}
                                    </a>
                                    <span class="badge bg-secondary">ZIP</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('dashboard.cn.executiva.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
