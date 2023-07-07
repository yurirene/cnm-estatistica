<table  width="100%">
    <tr>
        <td>UMPs Organizadas</td>
        <td align="right"><span class="badge bg-primary ">{{ $formulario->estrutura['ump_organizada'] }}</span></td>
    </tr>
    <tr>
        <td>UMPs não Organizadas</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->estrutura['ump_nao_organizada'] }}</span></td>
    </tr>
    <tr>
        <td>UMPs que repassaram a ACI</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->aci['ump_repassaram'] }}</span></td>
    </tr>
    <tr>
        <td>UMPs que não repassaram a ACI</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->aci['ump_nao_repassaram'] }}</span></td>
    </tr>
</table>
