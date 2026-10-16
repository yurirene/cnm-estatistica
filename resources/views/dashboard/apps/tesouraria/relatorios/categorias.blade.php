@php
    use App\Services\Apps\TesourariaService;

    $textoQuantidade = function (int $quantidade): string {
        return $quantidade === 1 ? '1 categoria' : $quantidade . ' categorias';
    };
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório por Categoria</title>
    @include('dashboard.apps.tesouraria.relatorios.partes.estilos')
</head>
<body>
    @include('dashboard.apps.tesouraria.relatorios.partes.cabecalho', [
        'titulo' => 'Entrada e Saída por Categoria',
        'subtitulo' => 'Para onde o dinheiro veio e para onde foi em ' . $ano,
    ])

    @include('dashboard.apps.tesouraria.relatorios.partes.cartoes', [
        'cartoes' => [
            ['rotulo' => 'Total de receitas', 'valor' => TesourariaService::formatarValorRelatorio($relatorio['total_receitas']), 'tom' => 'entrada'],
            ['rotulo' => 'Total de despesas', 'valor' => TesourariaService::formatarValorRelatorio($relatorio['total_despesas']), 'tom' => 'saida'],
            ['rotulo' => 'Resultado', 'valor' => TesourariaService::formatarValorRelatorio($relatorio['resultado'], true), 'tom' => 'destaque'],
        ],
    ])

    <div style="margin-top:16px; border:1px solid #DDE2EA; padding:10px 12px;">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td><h2 style="margin:0;">Receitas × despesas</h2></td>
                <td style="text-align:right;" class="meta">
                    As despesas representam {{ number_format($relatorio['percentual_despesas'], 1, ',', '.') }}% das receitas
                </td>
            </tr>
        </table>
        <table style="width:100%; border-collapse:collapse; margin-top:8px;">
            <tr>
                <td style="width:70px;">Receitas</td>
                <td>
                    <div style="height:12px; background:#F0F3F8;">
                        <div style="width:{{ $relatorio['total_receitas'] > 0 ? 100 : 0 }}%; height:12px; background:#14917A;"></div>
                    </div>
                </td>
                <td class="num" style="width:90px; font-weight:bold;">{{ TesourariaService::formatarValorRelatorio($relatorio['total_receitas']) }}</td>
            </tr>
            <tr>
                <td>Despesas</td>
                <td>
                    <div style="height:12px; background:#F0F3F8;">
                        <div style="width:{{ min(100, $relatorio['percentual_despesas']) }}%; height:12px; background:#E27D3A;"></div>
                    </div>
                </td>
                <td class="num" style="font-weight:bold;">{{ TesourariaService::formatarValorRelatorio($relatorio['total_despesas']) }}</td>
            </tr>
        </table>
    </div>

    @foreach([
        ['titulo' => 'Receitas por categoria', 'cor' => '#14917A', 'linhas' => $relatorio['receitas'], 'total' => $relatorio['total_receitas'], 'rotulo' => 'Total de receitas', 'classe' => 'verde'],
        ['titulo' => 'Despesas por categoria', 'cor' => '#E27D3A', 'linhas' => $relatorio['despesas'], 'total' => $relatorio['total_despesas'], 'rotulo' => 'Total de despesas', 'classe' => 'laranja'],
    ] as $grupo)
        <div style="margin-top:16px;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td>
                        <span style="display:inline-block; width:8px; height:8px; background:{{ $grupo['cor'] }};"></span>
                        <span style="font-size:13px; font-weight:bold;">{{ $grupo['titulo'] }}</span>
                    </td>
                    <td style="text-align:right;" class="meta">{{ $textoQuantidade(count($grupo['linhas'])) }}</td>
                </tr>
            </table>
            @if(count($grupo['linhas']))
                <table style="width:100%; border-collapse:collapse; margin-top:6px;">
                    <tr>
                        @foreach($grupo['linhas'] as $linha)
                            <td style="width:{{ $linha['largura'] }}%; height:12px; background:{{ $linha['cor'] }};"></td>
                        @endforeach
                    </tr>
                </table>
            @endif
            <table class="dados">
                <tr>
                    <td class="th esq">Categoria</td>
                    <td class="th num" style="width:110px;">Valor</td>
                    <td class="th esq" style="width:170px;">% do total</td>
                </tr>
                @forelse($grupo['linhas'] as $linha)
                    <tr>
                        <td>
                            <span style="display:inline-block; width:8px; height:8px; background:{{ $linha['cor'] }};"></span>
                            {{ $linha['categoria'] }}
                        </td>
                        <td class="num">{{ TesourariaService::formatarValorRelatorio($linha['valor']) }}</td>
                        <td>
                            <table style="width:150px; border-collapse:collapse;">
                                <tr>
                                    <td style="width:100px; border:none; padding:0;">
                                        <div style="height:6px; background:#F0F3F8;">
                                            <div style="width:{{ $linha['largura'] }}%; height:6px; background:{{ $linha['cor'] }};"></div>
                                        </div>
                                    </td>
                                    <td style="border:none; padding:0 0 0 6px; font-size:10px;">{{ $linha['porcentagem'] }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Sem resultado</td>
                    </tr>
                @endforelse
                <tr class="total">
                    <td>{{ $grupo['rotulo'] }}</td>
                    <td class="num {{ $grupo['classe'] }}">{{ TesourariaService::formatarValorRelatorio($grupo['total']) }}</td>
                    <td>100,00%</td>
                </tr>
            </table>
        </div>
    @endforeach

    @include('dashboard.apps.tesouraria.relatorios.partes.rodape', [
        'pagina' => 1,
        'paginas' => 1,
    ])
</body>
</html>
