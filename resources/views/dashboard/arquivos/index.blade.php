@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Arquivos',
    'subtitulo' => 'Google Drive',
])

<div class="container-fluid mt--7">
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card shadow">
                @if(!empty($semAcesso))
                <div class="card-body text-center py-5">
                    <i class="fab fa-google-drive fa-3x text-muted mb-3"></i>
                    <h4>Acesso aos arquivos indisponível</h4>
                    <p class="text-muted mb-0">
                        @if(!empty($erroConexao))
                            Não foi possível conectar ao Google Drive. Solicite ao administrador.
                        @elseif(!empty($driveNaoConfigurado))
                            O Google Drive ainda não foi configurado no sistema.
                        @else
                            Sua pasta de acesso ainda não foi configurada. Solicite ao administrador.
                        @endif
                    </p>
                </div>
                @else
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 bg-transparent p-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('dashboard.arquivos.index') }}">Raiz</a>
                                    </li>
                                    @foreach($breadcrumbs as $crumb)
                                    <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                                        @if($loop->last)
                                            {{ $crumb['nome'] }}
                                        @else
                                            <a href="{{ route('dashboard.arquivos.index', ['pasta' => $crumb['caminho']]) }}">
                                                {{ $crumb['nome'] }}
                                            </a>
                                        @endif
                                    </li>
                                    @endforeach
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto text-right">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modal-nova-pasta">
                                <i class="fas fa-folder-plus"></i> Nova pasta
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-upload">
                                <i class="fas fa-upload"></i> Enviar arquivo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(empty($conteudo['pastas']) && empty($conteudo['arquivos']))
                    <div class="text-center text-muted py-5">
                        <i class="fab fa-google-drive fa-3x mb-3"></i>
                        <p class="mb-0">Esta pasta está vazia.</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Tipo</th>
                                    <th>Tamanho</th>
                                    <th>Modificado</th>
                                    <th class="text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($conteudo['pastas'] as $pasta)
                                <tr>
                                    <td>
                                        <a href="{{ route('dashboard.arquivos.index', ['pasta' => $pasta['caminho']]) }}">
                                            <i class="fas fa-folder text-warning mr-2"></i>{{ $pasta['nome'] }}
                                        </a>
                                    </td>
                                    <td>Pasta</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td class="text-right">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary btn-renomear"
                                            data-toggle="modal"
                                            data-target="#modal-renomear"
                                            data-caminho="{{ $pasta['caminho'] }}"
                                            data-nome="{{ $pasta['nome'] }}"
                                            data-tipo="pasta"
                                            title="Renomear">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('dashboard.arquivos.excluir') }}" class="d-inline"
                                            onsubmit="return confirm('Deseja excluir esta pasta e todo o seu conteúdo?');">
                                            @csrf
                                            <input type="hidden" name="caminho" value="{{ $pasta['caminho'] }}">
                                            <input type="hidden" name="tipo" value="pasta">
                                            <input type="hidden" name="pasta_atual" value="{{ $conteudo['pasta_atual'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach

                                @foreach($conteudo['arquivos'] as $arquivo)
                                <tr>
                                    <td>
                                        <i class="fas fa-file text-primary mr-2"></i>{{ $arquivo['nome'] }}
                                    </td>
                                    <td>Arquivo</td>
                                    <td>{{ number_format($arquivo['tamanho'] / 1024, 1, ',', '.') }} KB</td>
                                    <td>{{ date('d/m/Y H:i', $arquivo['modificado']) }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('dashboard.arquivos.visualizar', ['caminho' => $arquivo['caminho']]) }}"
                                            class="btn btn-sm btn-outline-info"
                                            target="_blank"
                                            rel="noopener"
                                            title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary btn-renomear"
                                            data-toggle="modal"
                                            data-target="#modal-renomear"
                                            data-caminho="{{ $arquivo['caminho'] }}"
                                            data-nome="{{ $arquivo['nome'] }}"
                                            data-tipo="arquivo"
                                            title="Renomear">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ route('dashboard.arquivos.download', ['caminho' => $arquivo['caminho']]) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Baixar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form method="POST" action="{{ route('dashboard.arquivos.excluir') }}" class="d-inline"
                                            onsubmit="return confirm('Deseja excluir este arquivo?');">
                                            @csrf
                                            <input type="hidden" name="caminho" value="{{ $arquivo['caminho'] }}">
                                            <input type="hidden" name="tipo" value="arquivo">
                                            <input type="hidden" name="pasta_atual" value="{{ $conteudo['pasta_atual'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(empty($semAcesso))
<div class="modal fade" id="modal-upload" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('dashboard.arquivos.upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="pasta" value="{{ $conteudo['pasta_atual'] }}">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar arquivo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="arquivo">Arquivo</label>
                        <input type="file" name="arquivo" id="arquivo" class="form-control" required>
                        <small class="form-text text-muted">Tamanho máximo: 50 MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-nova-pasta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('dashboard.arquivos.pasta') }}">
                @csrf
                <input type="hidden" name="pasta" value="{{ $conteudo['pasta_atual'] }}">
                <div class="modal-header">
                    <h5 class="modal-title">Nova pasta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nome">Nome da pasta</label>
                        <input type="text" name="nome" id="nome" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-renomear" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('dashboard.arquivos.renomear') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="caminho" id="renomear-caminho">
                <input type="hidden" name="tipo" id="renomear-tipo">
                <input type="hidden" name="pasta_atual" value="{{ $conteudo['pasta_atual'] }}">
                <div class="modal-header">
                    <h5 class="modal-title">Renomear</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="renomear-nome">Novo nome</label>
                        <input type="text" name="nome" id="renomear-nome" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('js')
<script>
    $('#modal-renomear').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('#renomear-caminho').val(button.data('caminho'));
        $('#renomear-tipo').val(button.data('tipo'));
        $('#renomear-nome').val(button.data('nome'));
    });
</script>
@endpush
