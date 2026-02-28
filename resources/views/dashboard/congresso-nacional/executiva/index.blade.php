@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Congresso Nacional - Gerenciamento de Delegados'
])

@if(session('mensagem'))
    <div class="alert alert-{{ session('mensagem')['status'] ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
        {{ session('mensagem')['texto'] }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

<div class="container-fluid mt--7">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @if($reuniao ?? null)
                        <div class="card card-body py-2 px-3 mb-0 d-flex flex-row align-items-center gap-2">
                            <span class="text-muted">Reunião aberta:</span>
                            <strong>{{ $reuniao->nome }}</strong>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-copiar-id-reuniao" data-id="{{ $reuniao->id }}" title="Copiar ID da reunião">
                                <i class="fas fa-copy"></i> Copiar ID
                            </button>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0 py-2">
                            Nenhuma reunião aberta. Cadastre uma reunião para gerenciar delegados e documentos.
                        </div>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovaReuniao">
                        <i class="fas fa-plus"></i> Nova Reunião
                    </button>
                    <a href="{{ route('dashboard.cn.executiva.sincronizar-inscritos') }}" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> Sincronizar Inscritos
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-lg-6 mt-3">
            <div class="card card-stats mb-4 mb-xl-0 h-100">
                <div class="card-header h-100">
                    <div class="row  d-flex align-items-center">
                        <div class="col-8">
                            <h5 class="card-title text-uppercase text-muted mb-0">
                                Sinodais
                            </h5>
                        </div>
                        <div class="col-4 text-center">
                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <span class="h2 font-weight-bold mb-0">
                                {{ $totalizador['sinodais_com_delegado'] }} de {{ $totalizador['total_sinodais'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mt-3">
            <div class="card card-stats mb-4 mb-xl-0 h-100">
                <div class="card-header h-100">
                    <div class="row  d-flex align-items-center">
                        <div class="col-8">
                            <h5 class="card-title text-uppercase text-muted mb-0">
                                Federações
                            </h5>
                        </div>
                        <div class="col-4 text-center">
                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <span class="h2 font-weight-bold mb-0">
                            {{ $totalizador['federacoes_com_delegado'] }} de {{ $totalizador['total_federacoes'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mt-3">
            <div class="card card-stats mb-4 mb-xl-0 h-100">
                <div class="card-header h-100">
                    <div class="row  d-flex align-items-center">
                        <div class="col-8">
                            <h5 class="card-title text-uppercase text-muted mb-0">
                                Quórum
                            </h5>
                        </div>
                        <div class="col-4 text-center">
                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <span class="h2 font-weight-bold mb-0">
                                Sinodais {{ $totalizador['quorum_sinodais'] }}
                                <br>
                                Federações {{ $totalizador['quorum_federacoes'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Delegados das Federações</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Pago</th>
                                    <th>Credencial</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Telefone</th>
                                    <th>Federação</th>
                                    <th>Sinodal</th>
                                    <th>Oficial</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($delegadosFederacao as $delegado)
                                    <tr>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input
                                                    class="form-check-input check-status"
                                                    type="checkbox"
                                                    role="switch"
                                                    data-delegado-id="{{ $delegado->id }}"
                                                    data-tipo="federacao"
                                                    data-campo="pago"
                                                    {{ $delegado->pago ? 'checked' : '' }}
                                                >
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input
                                                    class="form-check-input check-status"
                                                    type="checkbox"
                                                    role="switch"
                                                    data-delegado-id="{{ $delegado->id }}"
                                                    data-tipo="federacao"
                                                    data-campo="credencial"
                                                    {{ $delegado->credencial ? 'checked' : '' }}
                                                >
                                            </div>
                                        </td>
                                        <td>{!! $delegado->status_formatado !!}</td>
                                        <td>
                                            @if($delegado->path_credencial)
                                                <a href="/{{ $delegado->path_credencial }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Ver Credencial
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $delegado->nome }}</td>
                                        <td>{{ $delegado->cpf }}</td>
                                        <td>{{ $delegado->telefone }}</td>
                                        <td>{{ $delegado->federacao->nome ?? '-' }}</td>
                                        <td>{{ $delegado->sinodal->nome ?? '-' }}</td>
                                        <td>
                                            @if($delegado->oficial == 1)
                                                Diácono
                                            @elseif($delegado->oficial == 2)
                                                Presbítero
                                            @else
                                                Não
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Nenhum delegado de federação cadastrado</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Delegados das Sinodais</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Pago</th>
                                    <th>Credencial</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                    <th>Nome</th>
                                    <th>Telefone</th>
                                    <th>Sinodal</th>
                                    <th>Oficial</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($delegadosSinodal as $delegado)
                                    <tr>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input
                                                    class="form-check-input check-status"
                                                    type="checkbox"
                                                    role="switch"
                                                    data-delegado-id="{{ $delegado->id }}"
                                                    data-tipo="sinodal"
                                                    data-campo="pago"
                                                    {{ $delegado->pago ? 'checked' : '' }}
                                                >
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input
                                                    class="form-check-input check-status"
                                                    type="checkbox"
                                                    role="switch"
                                                    data-delegado-id="{{ $delegado->id }}"
                                                    data-tipo="sinodal"
                                                    data-campo="credencial"
                                                    {{ $delegado->credencial ? 'checked' : '' }}
                                                >
                                            </div>
                                        </td>
                                        <td>{!! $delegado->status_formatado !!}</td>
                                        <td>
                                            @if($delegado->path_credencial ?? null)
                                                <a href="/{{ $delegado->path_credencial }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Ver Credencial
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $delegado->nome }}</td>
                                        <td>{{ $delegado->telefone }}</td>
                                        <td>{{ $delegado->sinodal->nome ?? '-' }}</td>
                                        <td>
                                            @if($delegado->oficial == 1)
                                                Diácono
                                            @elseif($delegado->oficial == 2)
                                                Presbítero
                                            @else
                                                Não
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Nenhum delegado de sinodal cadastrado</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-12">
            @include('dashboard.congresso-nacional.executiva.documentos')
        </div>
    </div>
</div>

{{-- Modal Nova Reunião --}}
<div class="modal fade" id="modalNovaReuniao" tabindex="-1" aria-labelledby="modalNovaReuniaoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dashboard.cn.executiva.reuniao.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovaReuniaoLabel">Cadastrar Reunião</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Ao cadastrar uma nova reunião, as demais serão automaticamente encerradas (status inativo).</p>
                    <div class="mb-3">
                        <label for="reuniao_nome" class="form-label">Nome da reunião <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nome') is-invalid @enderror" id="reuniao_nome" name="nome" value="{{ old('nome') }}" required maxlength="255" placeholder="Ex: Reunião 2025">
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(() => {
        const ROTA_PAGO = "{{ route('dashboard.cn.executiva.delegado.update', ':id') }}";
        const TOKEN = "{{ csrf_token() }}";

        $('.btn-copiar-id-reuniao').on('click', function() {
            const id = $(this).data('id').toString();
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(id).then(() => {
                    iziToast.success({ title: 'Copiado!', message: 'ID da reunião copiado: ' + id, position: 'topRight' });
                }).catch(() => fallbackCopy(id));
            } else {
                fallbackCopy(id);
            }
        });
        function fallbackCopy(text) {
            const ta = document.createElement('textarea');
            ta.value = text;
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy');
                iziToast.success({ title: 'Copiado!', message: 'ID da reunião: ' + text, position: 'topRight' });
            } catch (e) {
                iziToast.info({ title: 'ID da reunião', message: text, position: 'topRight' });
            }
            document.body.removeChild(ta);
        }

        $('.check-status').on('change', function() {
            const dados = $(this).data();
            const valor = ($(this).prop('checked'));
            $.ajax({
                url: ROTA_PAGO.replace(":id", dados.delegadoId),
                type: "PUT",
                data: {
                    _token: TOKEN,
                    tipo: dados.campo,
                    valor: valor ? 1 : 0,
                },
                success: function(response) {

                    iziToast.show({
                        title: 'Sucesso!',
                        message: response.mensagem,
                        position: 'topRight',
                    });
                },
                error: function(error){

                    iziToast.show({
                        title: 'Erro!',
                        message: response.mensagem,
                        position: 'topRight',
                    });
                }
            });
        })
    })
</script>
@endpush

