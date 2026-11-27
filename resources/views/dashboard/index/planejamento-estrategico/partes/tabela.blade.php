<div class="row mt-4" id="pe-tabela">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-transparent border-0">
                <h3 class="mb-1">Dados consolidados por ano</h3>
                <p class="text-muted mb-0 text-sm">Realizado (acima) e meta planejada (abaixo) para cada indicador do quinquênio.</p>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush pe-data-table mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Objetivo</th>
                            <th>Indicador</th>
                            @foreach($painel['anos'] as $ano)
                                <th class="text-center {{ $ano === $painel['anos'][count($painel['anos']) - 1] ? 'pe-meta-col' : '' }}">
                                    {{ $ano }}{{ $ano === $painel['anos'][count($painel['anos']) - 1] ? ' (Meta)' : '' }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($painel['tabela'] as $linha)
                            <tr class="{{ $linha['principal'] ? 'pe-row-principal' : '' }}">
                                <td class="font-weight-bold text-primary">{{ $linha['objetivo'] }}</td>
                                <td>
                                    {{ $linha['indicador'] }}
                                    @if($linha['principal'])
                                        <span class="badge badge-primary badge-sm ml-1">Principal</span>
                                    @endif
                                </td>
                                @foreach($painel['anos'] as $ano)
                                    @php $v = $linha['valores'][$ano] ?? null; @endphp
                                    <td class="text-center">
                                        <div class="pe-cell-real">
                                            {{ is_null($v['realizado'] ?? null) ? '—' : number_format((float) $v['realizado'], 1, ',', '.') . '%' }}
                                        </div>
                                        <div class="pe-cell-meta">
                                            meta {{ is_null($v['meta'] ?? null) ? '—' : number_format((float) $v['meta'], 0, ',', '.') . '%' }}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
