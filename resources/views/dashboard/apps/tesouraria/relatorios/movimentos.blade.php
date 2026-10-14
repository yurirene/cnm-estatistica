@php
    use App\Services\Apps\TesourariaService;

    $linhas = $relatorio;
    if (count($linhas) === 0) {
        $paginasLista = [[]];
    } else {
        $primeira = array_slice($linhas, 0, 10);
        $resto = array_slice($linhas, 10);
        $paginasLista = array_merge([$primeira], $resto ? array_chunk($resto, 18) : []);
    }
    $totalPaginas = count($paginasLista);
    $quantidade = count($linhas);
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de Movimentos</title>
    @include('dashboard.apps.tesouraria.relatorios.partes.estilos')
</head>
<body>
    @foreach($paginasLista as $indicePagina => $movimentos)
        @include('dashboard.apps.tesouraria.relatorios.partes.cabecalho', [
            'titulo' => 'Movimentos',
            'subtitulo' => 'Extrato dos lançamentos em ordem cronológica, com saldo após cada movimento',
            'compacto' => $indicePagina > 0,
        ])

        @if($indicePagina === 0)

            @include('dashboard.apps.tesouraria.relatorios.partes.cartoes', [
                'cartoes' => [
                    ['rotulo' => 'Saldo inicial', 'valor' => TesourariaService::formatarValorRelatorio($saldo_inicial), 'tom' => 'neutro'],
                    ['rotulo' => 'Entradas (' . $qtd_entradas . ')', 'valor' => TesourariaService::formatarValorRelatorio($entradas), 'tom' => 'entrada'],
                    ['rotulo' => 'Saídas (' . $qtd_saidas . ')', 'valor' => TesourariaService::formatarValorRelatorio($saidas), 'tom' => 'saida'],
                    ['rotulo' => 'Saldo final', 'valor' => TesourariaService::formatarValorRelatorio($saldo_final), 'tom' => 'destaque'],
                ],
            ])
        @endif

        <div style="margin-top:16px;">
            @if($indicePagina === 0)
                <h2>Lançamentos</h2>
            @endif
            <table class="dados">
                <tr>
                    <td class="th esq" style="width:72px;">Data</td>
                    <td class="th esq" style="width:72px;">Tipo</td>
                    <td class="th esq">Descrição</td>
                    <td class="th esq" style="width:100px;">Categoria</td>
                    <td class="th num" style="width:100px;">Valor</td>
                    <td class="th num" style="width:100px;">Saldo</td>
                </tr>
                @forelse($movimentos as $ordem => $movimento)
                    <tr style="{{ $ordem % 2 === 1 ? 'background:#FAFBFD;' : '' }}">
                        <td>{{ $movimento['data_movimento'] }}</td>
                        <td>
                            @if($movimento['entrada'])
                                <span class="pilula pilula-entrada">Entrada</span>
                            @else
                                <span class="pilula pilula-saida">Saída</span>
                            @endif
                        </td>
                        <td>{{ $movimento['descricao'] }}</td>
                        <td class="{{ $movimento['sem_categoria'] ? 'mudo' : '' }}" style="{{ $movimento['sem_categoria'] ? 'font-style:italic;' : '' }}">
                            {{ $movimento['categoria'] }}
                        </td>
                        <td class="num {{ $movimento['entrada'] ? 'verde' : 'laranja' }}" style="font-weight:bold;">{{ $movimento['valor'] }}</td>
                        <td class="num">{{ $movimento['saldo'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:#5B6577;">Sem resultado</td>
                    </tr>
                @endforelse
                @if($indicePagina === $totalPaginas - 1)
                    <tr class="total">
                        <td colspan="4">Movimentação líquida do período</td>
                        <td class="num {{ $liquido < 0 ? 'laranja' : 'verde' }}">{{ TesourariaService::formatarValorRelatorio($liquido, true) }}</td>
                        <td class="num">{{ TesourariaService::formatarValorRelatorio($saldo_final) }}</td>
                    </tr>
                @endif
            </table>
            @if($indicePagina === $totalPaginas - 1)
                <div style="margin-top:8px; font-size:10px; color:#5B6577;">
                    {{ $quantidade === 1 ? '1 lançamento' : $quantidade . ' lançamentos' }}
                    · Saldo inicial {{ TesourariaService::formatarValorRelatorio($saldo_inicial) }}
                    + entradas {{ TesourariaService::formatarValorRelatorio($entradas) }}
                    − saídas {{ TesourariaService::formatarValorRelatorio($saidas) }}
                    = saldo final {{ TesourariaService::formatarValorRelatorio($saldo_final) }}
                </div>
            @endif
        </div>

        @if($indicePagina === $totalPaginas - 1)
            @include('dashboard.apps.tesouraria.relatorios.partes.rodape', [
                'pagina' => $totalPaginas,
                'paginas' => $totalPaginas,
            ])
        @else
            </div>
            <div class="quebra"></div>
        @endif
    @endforeach
</body>
</html>
