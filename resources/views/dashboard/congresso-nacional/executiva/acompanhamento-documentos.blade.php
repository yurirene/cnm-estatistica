<div class="row mt-5">
    <div class="col-xl-12 mb-5 mb-xl-0">
        <div class="card shadow p-3">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Entrega de Documentos (Federações e Sinodais)</h3>
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <a href="{{ route('dashboard.cn.executiva.sincronizar-documentos-instancias') }}" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Sincronizar
                        </a>
                        <a href="{{ route('dashboard.cn.executiva.exportar-documentos-instancias-csv') }}" class="btn btn-outline-primary">
                            <i class="fas fa-file-csv"></i> Exportar CSV
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body" style="max-height: 700px; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table table-striped" id="documentos-instancias-table">
                        <thead>
                            <tr>
                                <th>Instância</th>
                                <th>Tipo</th>
                                <th>Diretoria</th>
                                <th>Estatístico</th>
                                <th>Planejamento</th>
                                <th>Status</th>
                                <th>Ações</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documentosInstancias ?? [] as $doc)
                                @php $delegado = $doc->relationLoaded('primeiro_delegado') ? $doc->primeiro_delegado : $doc->delegado_para_exportacao; @endphp
                                <tr>
                                    <td>
                                        @if($doc->federacao_id)
                                            {{ $doc->federacao->nome ?? '-' }}
                                        @else
                                            {{ $doc->sinodal->nome ?? '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($doc->federacao_id)
                                        Federação
                                        @else
                                        Sinodal
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input check-doc-instancia"
                                                type="checkbox"
                                                role="switch"
                                                data-doc-instancia-id="{{ $doc->id }}"
                                                data-campo="diretoria"
                                                {{ $doc->diretoria ? 'checked' : '' }}
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input check-doc-instancia"
                                                type="checkbox"
                                                role="switch"
                                                data-doc-instancia-id="{{ $doc->id }}"
                                                data-campo="estatistico"
                                                {{ $doc->estatistico ? 'checked' : '' }}
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input check-doc-instancia"
                                                type="checkbox"
                                                role="switch"
                                                data-doc-instancia-id="{{ $doc->id }}"
                                                data-campo="planejamento"
                                                {{ $doc->planejamento ? 'checked' : '' }}
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input check-doc-instancia"
                                                type="checkbox"
                                                role="switch"
                                                data-doc-instancia-id="{{ $doc->id }}"
                                                data-campo="status"
                                                {{ $doc->status ? 'checked' : '' }}
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        @if($delegado)
                                            <a href="{{ route('dashboard.cn.executiva.delegado.exportar-diretoria', ['delegado' => $delegado->id, 'exibir' => true]) }}" target="_blank" class="btn btn-sm btn-outline-primary me-1" title="Exportar Diretoria">
                                                <i class="fas fa-file-pdf"></i> Diretoria
                                            </a>
                                            <a href="{{ route('dashboard.cn.executiva.delegado.exportar-relatorio-estatistico', ['delegado' => $delegado->id, 'exibir' => true]) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Exportar Relatório Estatístico">
                                                <i class="fas fa-chart-bar"></i> Estatístico
                                            </a>
                                            <a href="/{{ $delegado->path_credencial }}" target="_blank" class="btn btn-sm btn-outline-success" title="Exportar Planejamento">
                                                <i class="fas fa-file-pdf"></i> Credencial
                                            </a>
                                        @else
                                            <span class="text-muted small">Sem delegado</span>
                                        @endif
                                    </td>
                                    <td>{{ $doc->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nenhuma instância cadastrada. Clique em <strong>Sincronizar</strong> para criar a partir dos delegados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
$(document).ready(function() {
    const ROTA_DOC_INSTANCIA = "{{ route('dashboard.cn.executiva.documento-instancia.update', ':id') }}";
    const TOKEN_DOC = "{{ csrf_token() }}";

    $('.check-doc-instancia').on('change', function() {
        const id = $(this).data('doc-instancia-id');
        const campo = $(this).data('campo');
        const valor = $(this).prop('checked');

        $.ajax({
            url: ROTA_DOC_INSTANCIA.replace(':id', id),
            type: 'PUT',
            data: {
                _token: TOKEN_DOC,
                campo: campo,
                valor: valor ? 1 : 0,
            },
            success: function(response) {
                if (typeof iziToast !== 'undefined') {
                    iziToast.success({
                        title: 'Sucesso!',
                        message: response.mensagem,
                        position: 'topRight',
                    });
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON && xhr.responseJSON.mensagem ? xhr.responseJSON.mensagem : 'Erro ao atualizar.';
                if (typeof iziToast !== 'undefined') {
                    iziToast.error({
                        title: 'Erro!',
                        message: msg,
                        position: 'topRight',
                    });
                }
            }
        });
    });
    $('#documentos-instancias-table').DataTable({
        lengthMenu: [100, 200, 500, -1],
        language: {
            url: '/vendor/datatables/portugues.json',
        }
    });
});
</script>
@endpush
