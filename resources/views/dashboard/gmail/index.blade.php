@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'E-mails',
    'subtitulo' => 'Gmail',
])

<div class="container-fluid mt--7">
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card shadow">
                @if(!empty($gmailNaoConfigurado))
                <div class="card-body text-center py-5">
                    <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                    <h4>Gmail ainda não configurado</h4>
                    <p class="text-muted mb-0">A conta de serviço e o e-mail da caixa ainda não foram configurados no sistema.</p>
                </div>
                @elseif(!empty($filtroNaoConfigurado))
                <div class="card-body text-center py-5">
                    <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                    <h4>Filtro de e-mail não configurado</h4>
                    <p class="text-muted mb-0">Peça ao master para definir o alias ou o marcador no seu usuário.</p>
                </div>
                @elseif(!empty($erroConexao))
                <div class="card-body text-center py-5">
                    <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                    <h4>Não foi possível listar os e-mails</h4>
                    <p class="text-muted mb-0">{{ $erroMensagem }}</p>
                </div>
                @else
                <div class="card-header border-0">
                    <h3 class="mb-0">Caixa</h3>
                    <p class="text-muted small mb-0 mt-2">
                        Alias: <strong>{{ auth()->user()->gmail_alias ?: 'não definido' }}</strong>
                        <span class="mx-2">·</span>
                        Marcador: <strong>{{ auth()->user()->gmail_label ?: 'não definido' }}</strong>
                    </p>
                </div>
                <div class="card-body">
                    @if(empty($mensagens))
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p class="mb-0">Nenhum e-mail encontrado para este filtro.</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-right">Ações</th>
                                    <th>Assunto</th>
                                    <th>Remetente</th>
                                    <th>Data</th>
                                    <th>Trecho</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mensagens as $mensagem)
                                <tr>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-sm btn-primary js-ver-email" data-id="{{ $mensagem['id'] }}">
                                            Ver
                                        </button>
                                    </td>
                                    <td>{{ $mensagem['subject'] !== '' ? $mensagem['subject'] : '(sem assunto)' }}</td>
                                    <td>{{ $mensagem['from'] !== '' ? $mensagem['from'] : '—' }}</td>
                                    <td>{{ $mensagem['date'] !== '' ? $mensagem['date'] : '—' }}</td>
                                    <td>{{ $mensagem['snippet'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(!empty($nextPageToken))
                    <div class="text-right mt-3">
                        <a class="btn btn-sm btn-primary" href="{{ route('dashboard.gmail.index', ['pageToken' => $nextPageToken]) }}">
                            Próxima página
                        </a>
                    </div>
                    @endif
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-email" tabindex="-1" role="dialog" aria-labelledby="modal-email-titulo" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-email-titulo">E-mail</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="email-carregando" class="text-muted mb-0">Carregando...</p>
                <p id="email-erro" class="text-danger d-none mb-0"></p>
                <div id="email-conteudo" class="d-none">
                    <p class="mb-1"><strong>De:</strong> <span id="email-de"></span></p>
                    <p class="mb-3"><strong>Data:</strong> <span id="email-data"></span></p>
                    <iframe id="email-html" class="w-100 border rounded d-none" sandbox style="min-height: 320px;" title="Conteúdo do e-mail"></iframe>
                    <pre id="email-texto" class="border rounded p-3 d-none mb-0" style="white-space: pre-wrap;"></pre>
                    <div id="email-anexos" class="mt-3 d-none">
                        <h6>Anexos</h6>
                        <ul id="email-lista-anexos" class="mb-0"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.querySelectorAll('.js-ver-email').forEach(function (botao) {
    botao.addEventListener('click', function () {
        var id = botao.getAttribute('data-id');
        var carregando = document.getElementById('email-carregando');
        var erro = document.getElementById('email-erro');
        var conteudo = document.getElementById('email-conteudo');
        var quadro = document.getElementById('email-html');
        var texto = document.getElementById('email-texto');
        var anexos = document.getElementById('email-anexos');
        var lista = document.getElementById('email-lista-anexos');

        document.getElementById('modal-email-titulo').textContent = 'E-mail';
        document.getElementById('email-de').textContent = '';
        document.getElementById('email-data').textContent = '';
        quadro.removeAttribute('srcdoc');
        quadro.classList.add('d-none');
        texto.textContent = '';
        texto.classList.add('d-none');
        lista.innerHTML = '';
        anexos.classList.add('d-none');
        erro.textContent = '';
        erro.classList.add('d-none');
        conteudo.classList.add('d-none');
        carregando.classList.remove('d-none');
        $('#modal-email').modal('show');

        fetch(@json(url('/dashboard/gmail')) + '/' + encodeURIComponent(id), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function (resposta) {
            return resposta.json().then(function (dados) {
                if (!resposta.ok) {
                    throw new Error(dados.message || 'Não foi possível abrir o e-mail.');
                }
                return dados;
            });
        }).then(function (dados) {
            carregando.classList.add('d-none');
            conteudo.classList.remove('d-none');
            document.getElementById('modal-email-titulo').textContent = dados.subject || '(sem assunto)';
            document.getElementById('email-de').textContent = dados.from || '—';
            document.getElementById('email-data').textContent = dados.date || '—';

            if (dados.html) {
                quadro.srcdoc = dados.html;
                quadro.classList.remove('d-none');
            } else {
                texto.textContent = dados.text || 'Este e-mail não tem conteúdo de texto.';
                texto.classList.remove('d-none');
            }

            if (dados.attachments && dados.attachments.length) {
                dados.attachments.forEach(function (anexo) {
                    var item = document.createElement('li');
                    var link = document.createElement('a');
                    link.href = @json(url('/dashboard/gmail')) + '/' + encodeURIComponent(id) + '/anexos/' + encodeURIComponent(anexo.index);
                    link.textContent = anexo.filename || 'anexo';
                    item.appendChild(link);
                    lista.appendChild(item);
                });
                anexos.classList.remove('d-none');
            }
        }).catch(function (falha) {
            carregando.classList.add('d-none');
            erro.textContent = falha.message || 'Não foi possível abrir o e-mail.';
            erro.classList.remove('d-none');
        });
    });
});
</script>
@endpush
