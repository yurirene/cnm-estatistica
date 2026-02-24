@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Congresso Nacional - Gerenciamento de Delegados'
])

<div class="container-fluid mt--7">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-end">
                <a href="{{ route('dashboard.cn.executiva.sincronizar-inscritos') }}" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i> Sincronizar Inscritos
                </a>
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
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Telefone</th>
                                    <th>Federação</th>
                                    <th>Sinodal</th>
                                    <th>Oficial</th>
                                    <th>Pago</th>
                                    <th>Credencial</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($delegadosFederacao as $delegado)
                                    <tr>
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
                                    <th>Nome</th>
                                    <th>Telefone</th>
                                    <th>Sinodal</th>
                                    <th>Oficial</th>
                                    <th>Pago</th>
                                    <th>Credencial</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($delegadosSinodal as $delegado)
                                    <tr>
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
</div>
@endsection

@push('js')
<script>
    $(document).ready(() => {
        const ROTA_PAGO = "{{ route('dashboard.cn.executiva.delegado.update', ':id') }}";
        const TOKEN = "{{ csrf_token() }}";

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

