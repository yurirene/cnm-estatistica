<table  width="100%">
    <tr>
        <td>Treinamentos participados da Federação</td>
        <td align="right"><span class="badge bg-primary ">{{ $formulario->organizacao['treinamentos_participados_federacao'] ?? 0 }}</span></td>
    </tr>
    <tr>
        <td>Treinamentos participados da Sinodal</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->organizacao['treinamentos_participados_sinodal'] ?? 0 }}</span></td>
    </tr>
    <tr>
        <td>Treinamentos participados da CNM</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->organizacao['treinamentos_participados_cnm'] ?? 0 }}</span></td>
    </tr>
</table>
