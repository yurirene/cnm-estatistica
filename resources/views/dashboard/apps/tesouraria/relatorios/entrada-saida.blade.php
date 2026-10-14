@php
    use App\Services\Apps\TesourariaService;

    $maximo = 0;
    foreach ($relatorio['dados'] as $mes) {
        $maximo = max($maximo, $mes['receita'], $mes['despesa']);
    }
    $teto = $maximo > 0 ? $maximo : 1;
    $alturaPlot = 88;
    $metade = (int) ($alturaPlot / 2);
    $rotuloEixo = function (float $valor): string {
        return number_format($valor, $valor == floor($valor) ? 0 : 2, ',', '.');
    };
    $barrasDoMes = function (array $mes) use ($teto, $alturaPlot): array {
        $barras = [];
        if ($mes['receita'] > 0) {
            $barras[] = [
                'altura' => (int) max(2, round(($mes['receita'] / $teto) * $alturaPlot)),
                'cor' => '#14917A',
                'texto' => '#0E7C66',
                'rotulo' => number_format($mes['receita'], $mes['receita'] == floor($mes['receita']) ? 0 : 2, ',', '.'),
            ];
        }
        if ($mes['despesa'] > 0) {
            $barras[] = [
                'altura' => (int) max(2, round(($mes['despesa'] / $teto) * $alturaPlot)),
                'cor' => '#E27D3A',
                'texto' => '#B4531A',
                'rotulo' => number_format($mes['despesa'], $mes['despesa'] == floor($mes['despesa']) ? 0 : 2, ',', '.'),
            ];
        }

        return $barras;
    };
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório Entrada e Saída</title>
    @include('dashboard.apps.tesouraria.relatorios.partes.estilos')
</head>
<body>
    @include('dashboard.apps.tesouraria.relatorios.partes.cabecalho', [
        'titulo' => 'Entrada e Saída',
        'subtitulo' => 'Resultado mensal e saldo acumulado de janeiro a dezembro de ' . $ano,
    ])

    @include('dashboard.apps.tesouraria.relatorios.partes.cartoes', [
        'cartoes' => [
            ['rotulo' => 'Saldo inicial', 'valor' => TesourariaService::formatarValorRelatorio($relatorio['saldo-inicial']), 'tom' => 'neutro'],
            ['rotulo' => 'Entradas', 'valor' => TesourariaService::formatarValorRelatorio($relatorio['total-receitas']), 'tom' => 'entrada'],
            ['rotulo' => 'Saídas', 'valor' => TesourariaService::formatarValorRelatorio($relatorio['total-despesas']), 'tom' => 'saida'],
            ['rotulo' => 'Saldo final', 'valor' => TesourariaService::formatarValorRelatorio($relatorio['saldo-final']), 'tom' => 'destaque'],
        ],
    ])

    <div style="margin-top:16px;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td><h2 style="margin:0;">Entradas e saídas por mês</h2></td>
                <td style="text-align:right; font-size:10px; color:#5B6577;">
                    <span style="display:inline-block; width:8px; height:8px; background:#14917A;"></span> Entradas
                    &nbsp;&nbsp;
                    <span style="display:inline-block; width:8px; height:8px; background:#E27D3A;"></span> Saídas
                </td>
            </tr>
        </table>
        <table style="width:100%; border-collapse:collapse; margin-top:6px;">
            <tr>
                <td style="width:36px; height:14px; vertical-align:bottom; text-align:right; font-size:8px; color:#5B6577; padding:0 4px 0 0;">
                    {{ $maximo > 0 ? $rotuloEixo($teto) : '0' }}
                </td>
                @foreach($relatorio['dados'] as $mes)
                    @php $barras = $barrasDoMes($mes); @endphp
                    <td style="height:14px; vertical-align:bottom; text-align:center; padding:0;">
                        @if(count($barras))
                            <table style="border-collapse:collapse; margin:0 auto;">
                                <tr>
                                    @foreach($barras as $barra)
                                        <td style="width:18px; text-align:center; font-size:7px; color:{{ $barra['texto'] }}; padding:0;">{{ $barra['rotulo'] }}</td>
                                    @endforeach
                                </tr>
                            </table>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                <td style="height:{{ $metade }}px; border-top:1px solid #E3E8EF;"></td>
                @foreach($relatorio['dados'] as $mes)
                    @php $barras = $barrasDoMes($mes); @endphp
                    <td style="height:{{ $metade }}px; vertical-align:bottom; border-top:1px solid #E3E8EF; padding:0;">
                        @if(count($barras))
                            <table style="border-collapse:collapse; margin:0 auto;">
                                <tr>
                                    @foreach($barras as $barra)
                                        <td style="width:18px; vertical-align:bottom; text-align:center; padding:0 2px;">
                                            @if($barra['altura'] > $metade)
                                                <table style="border-collapse:collapse; margin:0 auto;">
                                                    <tr>
                                                        <td style="width:12px; height:{{ $barra['altura'] - $metade }}px; background-color:{{ $barra['cor'] }}; font-size:1px; line-height:1px;">&nbsp;</td>
                                                    </tr>
                                                </table>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            </table>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                <td style="height:{{ $metade }}px; vertical-align:top; text-align:right; border-top:1px solid #E3E8EF; border-bottom:1px solid #14213D; font-size:8px; color:#5B6577; padding:0 4px 0 0;">
                    {{ $maximo > 0 ? $rotuloEixo($teto / 2) : '' }}
                </td>
                @foreach($relatorio['dados'] as $mes)
                    @php $barras = $barrasDoMes($mes); @endphp
                    <td style="height:{{ $metade }}px; vertical-align:bottom; border-top:1px solid #E3E8EF; border-bottom:1px solid #14213D; padding:0;">
                        @if(count($barras))
                            <table style="border-collapse:collapse; margin:0 auto;">
                                <tr>
                                    @foreach($barras as $barra)
                                        <td style="width:18px; vertical-align:bottom; text-align:center; padding:0 2px;">
                                            <table style="border-collapse:collapse; margin:0 auto;">
                                                <tr>
                                                    <td style="width:12px; height:{{ min($metade, $barra['altura']) }}px; background-color:{{ $barra['cor'] }}; font-size:1px; line-height:1px;">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                    @endforeach
                                </tr>
                            </table>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                <td style="text-align:right; font-size:8px; color:#5B6577; padding:2px 4px 0 0;">0</td>
                @foreach($relatorio['dados'] as $mes)
                    <td style="text-align:center; font-size:8px; padding-top:2px; color:{{ $mes['com_movimento'] ? '#14213D' : '#5B6577' }}; {{ $mes['com_movimento'] ? 'font-weight:bold;' : '' }}">
                        {{ $mes['sigla'] }}
                    </td>
                @endforeach
            </tr>
        </table>
    </div>

    <div style="margin-top:14px;">
        <h2>Detalhamento mensal</h2>
        <table class="dados">
            <tr>
                <td class="th esq">Período</td>
                <td class="th num">Entradas</td>
                <td class="th num">Saídas</td>
                <td class="th num">Resultado</td>
                <td class="th num">Saldo acumulado</td>
            </tr>
            @foreach($relatorio['dados'] as $mes)
                <tr class="{{ $mes['com_movimento'] ? 'destaque' : 'mudo' }}">
                    <td>{{ $mes['nome'] }}</td>
                    <td class="num {{ $mes['receita'] > 0 ? 'verde' : '' }}">
                        {{ $mes['receita'] > 0 ? TesourariaService::formatarValorRelatorio($mes['receita']) : '—' }}
                    </td>
                    <td class="num {{ $mes['despesa'] > 0 ? 'laranja' : '' }}">
                        {{ $mes['despesa'] > 0 ? TesourariaService::formatarValorRelatorio($mes['despesa']) : '—' }}
                    </td>
                    <td class="num {{ $mes['resultado'] > 0 ? 'verde' : ($mes['resultado'] < 0 ? 'laranja' : '') }}">
                        {{ $mes['com_movimento'] ? TesourariaService::formatarValorRelatorio($mes['resultado'], true) : '—' }}
                    </td>
                    <td class="num">{{ TesourariaService::formatarValorRelatorio($mes['saldo']) }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td>Total do exercício</td>
                <td class="num verde">{{ TesourariaService::formatarValorRelatorio($relatorio['total-receitas']) }}</td>
                <td class="num laranja">{{ TesourariaService::formatarValorRelatorio($relatorio['total-despesas']) }}</td>
                <td class="num {{ ($relatorio['total-receitas'] - $relatorio['total-despesas']) < 0 ? 'laranja' : 'verde' }}">
                    {{ TesourariaService::formatarValorRelatorio($relatorio['total-receitas'] - $relatorio['total-despesas'], true) }}
                </td>
                <td class="num">{{ TesourariaService::formatarValorRelatorio($relatorio['saldo-final']) }}</td>
            </tr>
        </table>
    </div>

    @include('dashboard.apps.tesouraria.relatorios.partes.rodape', [
        'pagina' => 1,
        'paginas' => 1,
    ])
</body>
</html>
