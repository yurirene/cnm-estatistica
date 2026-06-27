<div class="row">
    <div class="col-md-12">
        <h4>Documentos</h4>
        <p class="text-muted">O cadastro de documentos é realizado apenas pela sinodal.</p>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Data de Envio</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documentos ?? [] as $documento)
                        <tr>
                            <td>{{ $documento->titulo }}</td>
                            <td>{{ $documento->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($documento->status == 0)
                                    <span class="badge bg-warning">Pendente</span>
                                @elseif($documento->status == 1)
                                    <span class="badge bg-info">Visto</span>
                                @elseif($documento->status == 2)
                                    <span class="badge bg-success">Recebido</span>
                                @else
                                    <span class="badge bg-danger">Não Recebido</span>
                                @endif
                            </td>
                            <td>
                                <a href="/{{ $documento->path }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhum documento cadastrado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

