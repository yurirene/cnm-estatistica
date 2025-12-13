<div class="row">
    <div class="col-md-12">
        <h4>Documentos</h4>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        @include('dashboard.congresso-nacional.sinodal.form-documento')
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Ações</th>
                        <th>Título</th>
                        <th>Data de Envio</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documentos ?? [] as $documento)
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownDocumento{{ $documento->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        Ações
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownDocumento{{ $documento->id }}">
                                        <li>
                                            <a href="/{{ $documento->path }}" target="_blank" class="dropdown-item">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('dashboard.cn.sinodal.documento.delete', $documento->id) }}" class="dropdown-item text-danger" onclick="return confirm('Tem certeza que deseja excluir este documento?')">
                                                <i class="fas fa-trash"></i> Excluir
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
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

