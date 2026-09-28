@php
    use App\Models\AuxilioPmf\AuxilioPmfProposta;
    use App\Services\AuxilioPmfService;
@endphp

<div class="row">
    <div class="col-xl-12 mb-5">
        <div class="card shadow p-3">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">Minha inscrição</h3>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(!$edital)
                    <p class="text-muted mb-0">Não há edital disponível no momento.</p>
                @elseif($temRejeicaoNoAno && (!$proposta || $proposta->isRejeitada()))
                    <div class="alert alert-danger mb-0">
                        Sua proposta foi rejeitada neste ano. Não é possível enviar outra inscrição.
                    </div>
                @else
                    @if($proposta)
                        @php
                            $label = AuxilioPmfProposta::STATUS_LABELS[$proposta->status]
                                ?? AuxilioPmfProposta::STATUS_LABELS[AuxilioPmfProposta::STATUS_PENDENTE];
                        @endphp
                        <p>
                            <strong>Status:</strong>
                            <span class="badge badge-{{ $label['cor'] }}">{{ $label['texto'] }}</span>
                        </p>
                    @endif

                    {{-- Proposta --}}
                    <div class="border rounded p-3 mb-4">
                        <h4 class="mb-3">Proposta (PDF)</h4>

                        @if($proposta && $proposta->path_proposta)
                            <p>
                                <a href="{{ AuxilioPmfService::urlArquivo($proposta->path_proposta) }}"
                                   target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Visualizar proposta
                                </a>
                                @if($podeSubstituirProposta)
                                <form action="{{ route('dashboard.auxilio-pmf.proposta.destroy', $proposta->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Excluir o arquivo da proposta?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Excluir arquivo
                                    </button>
                                </form>
                                @endif
                            </p>
                        @endif

                        @if($podeEnviarProposta || ($podeSubstituirProposta && (!$proposta || !$proposta->path_proposta)))
                            {!! Form::open([
                                'method' => 'POST',
                                'route' => ['dashboard.auxilio-pmf.proposta.store', $edital->id],
                                'files' => true,
                            ]) !!}
                            <div class="form-group">
                                {!! Form::label('arquivo', 'Anexar PDF da proposta') !!}
                                {!! Form::file('arquivo', [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'accept' => 'application/pdf,.pdf',
                                ]) !!}
                            </div>
                            <button type="submit" class="btn btn-success">
                                Enviar proposta
                            </button>
                            {!! Form::close() !!}
                        @elseif(!$proposta)
                            <p class="text-muted mb-0">O recebimento de propostas está encerrado ou fora do prazo.</p>
                        @elseif($proposta->isPendente() && !$edital->estaRecebendo())
                            <p class="text-muted mb-0">Recebimento encerrado. Aguarde a análise da proposta.</p>
                        @endif
                    </div>

                    {{-- Relatório --}}
                    @if($proposta && $proposta->isAprovada())
                    <div class="border rounded p-3">
                        <h4 class="mb-3">Relatório de prestação de contas (PDF)</h4>

                        @if($proposta->path_relatorio)
                            <p>
                                <a href="{{ AuxilioPmfService::urlArquivo($proposta->path_relatorio) }}"
                                   target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Visualizar relatório
                                </a>
                                <form action="{{ route('dashboard.auxilio-pmf.relatorio.destroy', $proposta->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Excluir o arquivo do relatório?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Excluir arquivo
                                    </button>
                                </form>
                            </p>
                        @endif

                        @if(!$proposta->path_relatorio)
                            {!! Form::open([
                                'method' => 'POST',
                                'route' => ['dashboard.auxilio-pmf.relatorio.store', $proposta->id],
                                'files' => true,
                            ]) !!}
                            <div class="form-group">
                                {!! Form::label('arquivo', 'Anexar PDF do relatório') !!}
                                {!! Form::file('arquivo', [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'accept' => 'application/pdf,.pdf',
                                ]) !!}
                            </div>
                            <button type="submit" class="btn btn-success">
                                Enviar relatório
                            </button>
                            {!! Form::close() !!}
                        @endif
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
