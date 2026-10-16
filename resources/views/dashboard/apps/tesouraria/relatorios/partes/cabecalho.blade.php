<div class="folha">
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="width:48px; vertical-align:middle;">
                <img src="{{ public_path('img/logos/ump_comprimida.png') }}" width="55" alt="">
            </td>
            <td style="vertical-align:middle;">
                <div class="nome">{{ $nome }}</div>
            </td>
            <td style="vertical-align:middle; text-align:right;" class="meta">
                
            </td>
        </tr>
    </table>

    @if(empty($compacto))
        <div style="margin-top:14px; border-top:1px solid #DDE2EA; padding-top:12px;">
            <div class="olho">RELATÓRIO FINANCEIRO</div>
            <h1>{{ $titulo }}</h1>
            <div class="subtitulo">Exercício {{ $exercicio }}</div>
        </div>
    @endif
