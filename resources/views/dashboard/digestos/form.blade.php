@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Digestos',
    'botaoRetorno' => route('dashboard.digestos.index'),
])

@php
    $editando = isset($digesto);
    $arquivo = $arquivo ?? ['nome' => '', 'existe' => false, 'tamanho' => null, 'enviado_em' => null, 'url' => null];
    $comissoes = $comissoes ?? [];
@endphp

<div class="container-fluid mt--7 digesto-admin">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-body">
                    <div class="digesto-form-crumb mb-3">
                        Digestos / {{ $editando ? 'Editar documento' : 'Novo documento' }}
                    </div>

                    @if (!$editando)
                        {!! Form::open(['url' => route('dashboard.digestos.store'), 'method' => 'POST', 'files' => true, 'id' => 'form-digesto']) !!}
                    @else
                        {!! Form::model($digesto, ['url' => route('dashboard.digestos.update', $digesto->id), 'method' => 'PUT', 'files' => true, 'id' => 'form-digesto']) !!}
                    @endif

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="digesto-form-section">
                                <h4>Identificação</h4>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            {!! Form::label('titulo', 'Título') !!} <span class="digesto-req">*</span>
                                            {!! Form::text('titulo', null, ['class' => 'form-control', 'required' => true, 'autocomplete' => 'off', 'id' => 'titulo']) !!}
                                            <small class="form-text text-muted">Gerado automaticamente a partir de Nº do documento + Ano + Tipo. Edite se necessário.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('tipo_reuniao_id', 'Tipo de reunião') !!} <span class="digesto-req">*</span>
                                            {!! Form::select('tipo_reuniao_id', $tipos, null, ['class' => 'form-control', 'required' => true, 'id' => 'tipo_reuniao_id']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('ano', 'Ano') !!} <span class="digesto-req">*</span>
                                            {!! Form::text('ano', ! $editando ? date('Y') : null, ['class' => 'form-control', 'required' => true, 'autocomplete' => 'off', 'id' => 'ano', 'maxlength' => 4]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('tipo_documento', 'Tipo de documento') !!} <span class="digesto-req">*</span>
                                            {!! Form::select('tipo_documento', ['' => 'Selecione'] + $tiposDocumento, null, ['class' => 'form-control', 'required' => true, 'id' => 'tipo_documento']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('numero_documento', 'Número do documento') !!} <span class="digesto-req">*</span>
                                            {!! Form::text('numero_documento', null, ['class' => 'form-control', 'required' => true, 'autocomplete' => 'off', 'id' => 'numero_documento', 'placeholder' => 'Ex.: 05-CN']) !!}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            {!! Form::label('comissao', 'Comissão') !!}
                                            {!! Form::text('comissao', null, ['class' => 'form-control', 'autocomplete' => 'off', 'id' => 'comissao', 'list' => 'comissoes-list', 'placeholder' => 'Comece a digitar para buscar...']) !!}
                                            <datalist id="comissoes-list">
                                                @foreach($comissoes as $comissao)
                                                    <option value="{{ $comissao }}">
                                                @endforeach
                                            </datalist>
                                            <small class="form-text text-muted">Selecione uma comissão existente para manter os filtros de busca consistentes.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="digesto-form-section">
                                <h4>Arquivo</h4>
                                {!! Form::file('arquivo', array_filter([
                                    'class' => 'd-none',
                                    'id' => 'arquivo',
                                    'required' => $editando ? null : true,
                                ])) !!}
                                <div class="digesto-file-row">
                                    <i class="far fa-file-alt"></i>
                                    <div class="flex-grow-1">
                                        <div class="fname" id="digesto-file-name">
                                            {{ $arquivo['nome'] ?: 'Nenhum arquivo selecionado' }}
                                        </div>
                                        <div class="fmeta" id="digesto-file-meta">
                                            @if($editando && $arquivo['enviado_em'])
                                                Enviado em {{ $arquivo['enviado_em'] }}@if($arquivo['tamanho']) · {{ $arquivo['tamanho'] }}@endif
                                            @else
                                                PDF, DOC ou DOCX
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="digesto-escolher-arquivo">
                                        {{ $editando ? 'Substituir arquivo' : 'Escolher arquivo' }}
                                    </button>
                                </div>
                            </div>

                            <div class="digesto-form-section">
                                <h4>Conteúdo (indexado para busca)</h4>
                                <div class="form-group mb-1">
                                    {!! Form::label('texto', 'Texto completo') !!} <span class="digesto-req">*</span>
                                    {!! Form::textarea('texto', null, ['class' => 'form-control', 'rows' => 10, 'required' => true, 'id' => 'texto']) !!}
                                </div>
                                <div class="digesto-char-count" id="digesto-char-count">0 caracteres · usado para busca fulltext no Digesto público</div>
                            </div>

                            <div class="digesto-action-bar">
                                @if($editando)
                                    <button type="button" class="btn-link text-danger p-0" onclick="deleteRegistro('{{ route('dashboard.digestos.delete', $digesto->id) }}')">
                                        Excluir documento
                                    </button>
                                @else
                                    <span></span>
                                @endif
                                <div>
                                    <a href="{{ route('dashboard.digestos.index') }}" class="btn btn-secondary">Voltar</a>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-save"></i> {{ $editando ? 'Atualizar' : 'Cadastrar' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="digesto-preview-panel">
                                <div class="digesto-preview-label">Pré-visualização — busca pública</div>
                                <div class="digesto-preview-card">
                                    <div class="digesto-result-top">
                                        <span class="digesto-type-badge" id="preview-tipo">Tipo</span>
                                        <span class="digesto-meeting" id="preview-reuniao">Reunião · Ano</span>
                                    </div>
                                    <h3 class="digesto-result-title" id="preview-titulo">Título do documento</h3>
                                    <p class="digesto-result-snippet" id="preview-snippet">O trecho indexado aparece aqui conforme o texto completo.</p>
                                </div>

                                <div class="digesto-checklist">
                                    <h4>Checklist de publicação</h4>
                                    <div class="digesto-check-item pending" data-check="titulo"><span class="ic">!</span>Título preenchido</div>
                                    <div class="digesto-check-item pending" data-check="reuniao"><span class="ic">!</span>Tipo de reunião e ano definidos</div>
                                    <div class="digesto-check-item pending" data-check="tipo"><span class="ic">!</span>Tipo de documento selecionado</div>
                                    <div class="digesto-check-item pending" data-check="numero"><span class="ic">!</span>Número do documento informado</div>
                                    <div class="digesto-check-item pending" data-check="comissao"><span class="ic">!</span>Comissão vinculada</div>
                                    <div class="digesto-check-item pending" data-check="arquivo"><span class="ic">!</span>Arquivo anexado</div>
                                    <div class="digesto-check-item pending" data-check="texto"><span class="ic">!</span>Texto indexado para busca</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    (function () {
        var autoTitulo = {!! $editando ? 'false' : 'true' !!};
        var temArquivo = {!! ($editando && !empty($arquivo['nome'])) ? 'true' : 'false' !!};
        var tituloEl = document.getElementById('titulo');
        var tituloManual = false;

        function texto(id) {
            var el = document.getElementById(id);
            return el ? (el.value || '').trim() : '';
        }

        function labelSelect(id) {
            var el = document.getElementById(id);
            if (!el || el.selectedIndex < 0) return '';
            var opt = el.options[el.selectedIndex];
            if (!opt || !opt.value) return '';
            return opt.text.trim();
        }

        function montarTitulo() {
            var numero = texto('numero_documento');
            var ano = texto('ano');
            var tipo = labelSelect('tipo_documento');
            var comissao = texto('comissao');
            var prefixo = [numero, ano].filter(Boolean).join(' ');
            var resto = [tipo, comissao].filter(Boolean).join(' ');
            if (!prefixo && !resto) return '';
            return (prefixo + (resto ? ' - ' + resto : '')).toUpperCase();
        }

        function atualizarTitulo() {
            if (!autoTitulo || tituloManual || !tituloEl) return;
            tituloEl.value = montarTitulo();
        }

        function marcar(nome, ok) {
            var item = document.querySelector('[data-check="' + nome + '"]');
            if (!item) return;
            item.classList.toggle('done', ok);
            item.classList.toggle('pending', !ok);
            item.querySelector('.ic').textContent = ok ? '✓' : '!';
        }

        function atualizarChecklist() {
            marcar('titulo', texto('titulo') !== '');
            marcar('reuniao', texto('tipo_reuniao_id') !== '' && texto('ano') !== '');
            marcar('tipo', texto('tipo_documento') !== '');
            marcar('numero', texto('numero_documento') !== '');
            marcar('comissao', texto('comissao') !== '');
            marcar('arquivo', temArquivo);
            marcar('texto', texto('texto') !== '');
        }

        function atualizarPreview() {
            var tipoClasse = texto('tipo_documento') || '';
            var tipoLabel = labelSelect('tipo_documento') || 'Tipo';
            var reuniao = labelSelect('tipo_reuniao_id') || 'Reunião';
            var ano = texto('ano') || 'Ano';
            var titulo = texto('titulo') || 'Título do documento';
            var corpo = texto('texto');
            var snippet = corpo
                ? (corpo.length > 180 ? corpo.slice(0, 180) + '...' : corpo)
                : 'O trecho indexado aparece aqui conforme o texto completo.';

            var badge = document.getElementById('preview-tipo');
            badge.textContent = tipoLabel;
            badge.className = 'digesto-type-badge ' + tipoClasse;
            document.getElementById('preview-reuniao').textContent = reuniao + ' · ' + ano;
            document.getElementById('preview-titulo').textContent = titulo;
            document.getElementById('preview-snippet').textContent = snippet;

            var count = corpo.length;
            document.getElementById('digesto-char-count').textContent =
                count.toLocaleString('pt-BR') + ' caracteres · usado para busca fulltext no Digesto público';
        }

        function atualizar() {
            atualizarTitulo();
            atualizarPreview();
            atualizarChecklist();
        }

        ['tipo_reuniao_id', 'ano', 'tipo_documento', 'numero_documento', 'comissao', 'texto'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.addEventListener('input', atualizar);
            if (el) el.addEventListener('change', atualizar);
        });

        if (tituloEl) {
            tituloEl.addEventListener('input', function () {
                tituloManual = true;
                autoTitulo = false;
                atualizarPreview();
                atualizarChecklist();
            });
        }

        var arquivoInput = document.getElementById('arquivo');
        var escolher = document.getElementById('digesto-escolher-arquivo');
        if (escolher && arquivoInput) {
            escolher.addEventListener('click', function () {
                arquivoInput.click();
            });
            arquivoInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    temArquivo = true;
                    document.getElementById('digesto-file-name').textContent = this.files[0].name;
                    document.getElementById('digesto-file-meta').textContent =
                        Math.max(1, Math.round(this.files[0].size / 1024)) + ' KB';
                    atualizarChecklist();
                }
            });
        }

        atualizar();
    })();
</script>
@endpush
