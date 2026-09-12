<table  width="100%">
    <tr>
        <td>Treinamentos promovidos</td>
        <td align="right"><span class="badge bg-primary ">{{ $formulario->organizacao['treinamentos_promovidos'] ?? 0 }}</span></td>
    </tr>
    <tr>
        <td>Treinamentos participados da CNM</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->organizacao['treinamentos_participados_cnm'] ?? 0 }}</span></td>
    </tr>
</table>
