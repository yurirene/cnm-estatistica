<table  width="100%">

    <tr>
        <td>Federações Organizadas</td>
        <td align="right"><span class="badge bg-primary ">{{ $formulario->estrutura['federacao_organizada'] }}</span></td>
    </tr>
    <tr>
        <td>Federações não Organizadas</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->estrutura['federacao_nao_organizada'] }}</span></td>
    </tr>
    <tr>
        <td>Federações que repassaram a ACI</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->aci['federacao_repassaram'] }}</span></td>
    </tr>
    <tr>
        <td>Federações que não repassaram a ACI</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->aci['federacao_nao_repassaram'] }}</span></td>
    </tr>

</table>
