<table  width="100%">
    <tr>
        <td>Jovens que fizeram a trilha da CNM</td>
        <td align="right"><span class="badge bg-primary ">{{ $formulario->discipulado['trilha_cnm'] ?? 0 }}</span></td>
    </tr>
    <tr>
        <td>Jovens discipulando pelo método da CNM</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->discipulado['discipulando_cnm'] ?? 0 }}</span></td>
    </tr>
    <tr>
        <td>Jovens discipulando por outro método</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->discipulado['discipulando_outro'] ?? 0 }}</span></td>
    </tr>
    <tr>
        <td>Jovens sendo discipulados</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->discipulado['sendo_discipulados'] ?? 0 }}</span></td>
    </tr>
</table>
