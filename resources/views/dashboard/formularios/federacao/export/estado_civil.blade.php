<table  width="100%">
    <tr>
        <td>Solteiros</td>
        <td align="right"><span class="badge bg-primary ">{{ $formulario->estado_civil['solteiros'] }}</span></td>
    </tr>
    <tr>
        <td>Casados</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->estado_civil['casados'] }}</span></td>
    </tr>
    <tr>
        <td>Divorciados</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->estado_civil['divorciados'] }}</span></td>
    </tr>
    <tr>
        <td>Viúvos</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->estado_civil['viuvos'] }}</span></td>
    </tr>
    <tr>
        <td>Com filhos</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->estado_civil['filhos'] }}</span></td>
    </tr>
</table>