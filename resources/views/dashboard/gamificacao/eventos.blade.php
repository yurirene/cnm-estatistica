@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Eventos Game CNM',
    'subtitulo' => 'Participação em PMF, DJP, Treinamentos, Resgate e Bônus Missionário'
])

<div class="container-fluid mt--7 pb-5">
    <div class="card shadow mb-4 mt-5">
        <div class="card-header bg-transparent">
            <h3 class="mb-1">Lançar participação</h3>
            <p class="mb-0" style="font-size:13px;color:var(--color-muted)">
                PMF, DJP, Resgate e Bônus Missionário só podem ser lançados uma vez por instância no mesmo ano.
                Eventos esporádicos podem se repetir.
            </p>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.game-cnm.eventos.store') }}" id="form-evento">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-control-label" for="tipo">Tipo</label>
                        <select name="tipo" id="tipo" class="form-control" required>
                            @foreach($tipos as $valor => $tipo)
                                <option value="{{ $valor }}"
                                    data-natureza="{{ $tipo['natureza'] }}"
                                    data-unico="{{ $tipo['unico'] ? '1' : '0' }}"
                                    @selected(old('tipo') === $valor)
                                >
                                    {{ $tipo['label'] }} · {{ $tipo['pontos'] }} pts
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-control-label" for="referencia">Nome / referência</label>
                        <input type="text" name="referencia" id="referencia" class="form-control"
                               value="{{ old('referencia') }}" maxlength="255"
                               placeholder="Obrigatório em evento esporádico">
                        <small class="form-text text-muted">Nos demais tipos, se vazio, usa o nome do tipo.</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-control-label" for="ano">Ano de referência</label>
                        <select name="ano" id="ano" class="form-control" required>
                            @foreach($anos as $anoOpcao)
                                <option value="{{ $anoOpcao }}" @selected((int) old('ano', $ano) === $anoOpcao)>
                                    {{ $anoOpcao }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3" id="grupo-federacoes">
                    <label class="form-control-label" for="federacao_ids">Federações</label>
                    <select name="federacao_ids[]" id="federacao_ids" class="form-control isSelect2" multiple style="width:100%">
                        @foreach($federacoesPorSinodal as $sinodalNome => $federacoes)
                            <optgroup label="{{ $sinodalNome }}">
                                @foreach($federacoes as $federacao)
                                    <option value="{{ $federacao->id }}"
                                        @selected(collect(old('federacao_ids', []))->contains($federacao->id))
                                    >
                                        {{ $federacao->nome }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3" id="grupo-sinodais" style="display:none">
                    <label class="form-control-label" for="sinodal_ids">Sinodais</label>
                    <select name="sinodal_ids[]" id="sinodal_ids" class="form-control isSelect2" multiple style="width:100%">
                        @foreach($sinodais as $id => $nome)
                            <option value="{{ $id }}"
                                @selected(collect(old('sinodal_ids', []))->contains($id))
                            >
                                {{ $nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Lançar
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between flex-wrap" style="gap:12px">
            <h3 class="mb-0">Lançamentos de {{ $ano }}</h3>
            <form method="GET" action="{{ route('dashboard.game-cnm.eventos.index') }}" class="d-flex align-items-center" style="gap:8px">
                <select name="ano" class="form-control form-control-sm" onchange="this.form.submit()">
                    @foreach($anos as $anoOpcao)
                        <option value="{{ $anoOpcao }}" @selected($ano === $anoOpcao)>{{ $anoOpcao }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body p-0">
            @if($grupos->isEmpty())
                <p class="text-muted p-4 mb-0">Nenhuma participação lançada neste ano.</p>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Referência</th>
                                <th>Instância</th>
                                <th>Pts</th>
                                <th>Quando</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grupos as $grupo)
                                @foreach($grupo as $conquista)
                                    <tr>
                                        <td>{{ $conquista->tipo->label() }}</td>
                                        <td>{{ $conquista->referencia }}</td>
                                        <td>
                                            @if($conquista->sinodal)
                                                {{ $conquista->sinodal->nome }}
                                                <small class="text-muted d-block">Sinodal</small>
                                            @elseif($conquista->federacao)
                                                {{ $conquista->federacao->nome }}
                                                <small class="text-muted d-block">{{ $conquista->federacao->sinodal?->nome ?? 'Federação' }}</small>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $conquista->pontos }}</td>
                                        <td>{{ optional($conquista->concedido_em)->format('d/m/Y H:i') }}</td>
                                        <td class="text-right">
                                            <form method="POST"
                                                  action="{{ route('dashboard.game-cnm.eventos.destroy', $conquista) }}"
                                                  onsubmit="return confirm('Remover esta participação?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Remover
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@push('js')
<script>
    function atualizarNaturezaEvento() {
        var natureza = $('#tipo option:selected').data('natureza');
        if (natureza === 'sinodal') {
            $('#grupo-sinodais').show();
            $('#grupo-federacoes').hide();
            $('#federacao_ids').val(null).trigger('change');
        } else {
            $('#grupo-sinodais').hide();
            $('#grupo-federacoes').show();
            $('#sinodal_ids').val(null).trigger('change');
        }
    }
    $(function () {
        atualizarNaturezaEvento();
        $('#tipo').on('change', atualizarNaturezaEvento);
    });
</script>
@endpush
@endsection
