<table  width="100%">
    <tr>
        <td>Ensino Fundamental</td>
        <td align="right"><span class="badge bg-primary ">{{ $formulario->escolaridade['fundamental'] }}</span></td>
    </tr>
    <tr>
        <td>Ensino Médio</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->escolaridade['medio'] }}</span></td>
    </tr>
    <tr>
        <td>Ensino Técnico</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->escolaridade['tecnico'] }}</span></td>
    </tr>
    <tr>
        <td>Ensino Superior</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->escolaridade['superior'] }}</span></td>
    </tr>
    <tr>
        <td>Pós-Graduação</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->escolaridade['pos'] }}</span></td>
    </tr>
    <tr>
        <td>Desempregados</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->escolaridade['desempregado'] }}</span></td>
    </tr>
</table>