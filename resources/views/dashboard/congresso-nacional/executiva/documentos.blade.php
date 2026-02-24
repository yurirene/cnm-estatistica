<div class="col-xl-12 mb-5 mb-xl-0">
    <div class="card shadow p-3">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Documentos Recebidos</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Sinodal</th>
                            <th>Data de Envio</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documentos ?? [] as $documento)
                            <tr>
                                <td>{{ $documento->titulo }}</td>
                                <td>{{ $documento->sinodal->nome }}</td>
                                <td>{{ $documento->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($documento->status == 0)
                                        <span class="badge bg-warning">Pendente</span>
                                    @elseif($documento->status == 1)
                                        <span class="badge bg-info">Recebido</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input check-status-documento"
                                            type="checkbox"
                                            role="switch"
                                            data-documento-id="{{ $documento->id }}"
                                            {{ $documento->status == 1 ? 'checked' : '' }}
                                        >
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Nenhum documento cadastrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).ready(() => {
        const ROTA_DOC = "{{ route('dashboard.cn.executiva.documento.update', ':id') }}";
        const TOKEN_DOC = "{{ csrf_token() }}";

        $('.check-status-documento').on('change', function() {
            const dados = $(this).data();
            const valor = ($(this).prop('checked'));
            $.ajax({
                url: ROTA_DOC.replace(":id", dados.documentoId),
                type: "PUT",
                data: {
                    _token: TOKEN_DOC,
                    status: valor ? 1 : 0,
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
    });
</script>
@endpush