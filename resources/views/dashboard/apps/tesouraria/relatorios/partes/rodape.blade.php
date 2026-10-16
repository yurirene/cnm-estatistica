<table class="assinaturas">
    <tr>
        <td>
            <div class="nome">{{ $tesoureiro !== '' ? $tesoureiro : ' ' }}</div>
            <div class="meta">Tesoureiro(a)</div>
        </td>
        <td>
            <div class="nome">{{ $presidente !== '' ? $presidente : ' ' }}</div>
            <div class="meta">Presidente</div>
        </td>
    </tr>
</table>
<table class="rodape">
    <tr>
        <td>Gerado pela Plataforma em {{ $emitido_em }}</td>
        <td style="text-align:right;">Página {{ $pagina }} de {{ $paginas }}</td>
    </tr>
</table>
</div>
