@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Game CNM',
    'subtitulo' => 'Regras de pontuação do Planejamento Estratégico'
])

<div class="container-fluid mt--7 pb-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mt-5">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between" style="gap:12px">
                    <p class="mb-0" style="font-size:13px;color:var(--color-muted);max-width:640px">
                        Alterações valem nos próximos cálculos. Para atualizar placares já gravados, use o recálculo em lote.
                    </p>
                    <form method="POST" action="{{ route('dashboard.game-cnm.recalcular') }}"
                          onsubmit="return confirm('Recalcular todos os placares do ano de referência?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-sync-alt mr-1"></i> Recalcular placares
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach($grupos as $grupo)
        <div class="card shadow mb-4">
            <div class="card-header bg-transparent">
                <h3 class="mb-1">{{ $grupo['grupo'] }}</h3>
                <p class="mb-0" style="font-size:13px;color:var(--color-muted)">{{ $grupo['descricao'] }}</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dashboard.game-cnm.update') }}">
                    @csrf
                    <div class="row">
                        @foreach($grupo['campos'] as $campo)
                            @php
                                $nomeHtml = str_replace('.', '__', $campo['nome']);
                                $valoresAntigos = old('valores');
                                $valorCampo = is_array($valoresAntigos) && array_key_exists($nomeHtml, $valoresAntigos)
                                    ? $valoresAntigos[$nomeHtml]
                                    : $campo['valor'];
                            @endphp
                            <div class="col-md-6 col-xl-4 mb-3">
                                <label class="form-control-label" for="campo-{{ md5($campo['nome']) }}">
                                    {{ $campo['label'] }}
                                </label>
                                <input
                                    id="campo-{{ md5($campo['nome']) }}"
                                    class="form-control"
                                    name="valores[{{ $nomeHtml }}]"
                                    type="{{ $campo['tipo'] === 'number' ? 'number' : 'text' }}"
                                    @if($campo['tipo'] === 'number') min="0" @endif
                                    value="{{ $valorCampo }}"
                                    required
                                >
                                @if(!empty($campo['ajuda']))
                                    <small class="form-text text-muted">{{ $campo['ajuda'] }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($grupo['grupo'] === 'Ligas — Sinodal')
                        <p class="mb-3" style="font-size:12.5px;color:var(--color-muted)">
                            Faixas atuais: Ouro {{ $ligas['sinodal']['ouro'] }} ·
                            Prata {{ $ligas['sinodal']['prata'] }} ·
                            Bronze {{ $ligas['sinodal']['bronze'] }}
                        </p>
                    @endif
                    @if($grupo['grupo'] === 'Ligas — Federação')
                        <p class="mb-3" style="font-size:12.5px;color:var(--color-muted)">
                            Faixas atuais: Ouro {{ $ligas['federacao']['ouro'] }} ·
                            Prata {{ $ligas['federacao']['prata'] }} ·
                            Bronze {{ $ligas['federacao']['bronze'] }}
                        </p>
                    @endif

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Salvar {{ $grupo['grupo'] }}
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
