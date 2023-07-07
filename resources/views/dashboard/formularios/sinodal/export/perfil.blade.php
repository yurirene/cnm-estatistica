<table  width="100%">
    <tr>
        <td>Ativos</td>
        <td align="right"><span class="badge bg-primary ">{{ $formulario->perfil['ativos'] }}</span></td>
    </tr>
    <tr>
        <td>Cooperadores</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->perfil['cooperadores'] }}</span></td>
    </tr>
    <tr>
        <td>Menores de 19 anos</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->perfil['menor19'] }}</span></td>
    </tr>
    <tr>
        <td>Entre 19-23 anos</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->perfil['de19a23'] }}</span></td>
    </tr>
    <tr>
        <td>Entre 24-29 anos</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->perfil['de24a29'] }}</span></td>
    </tr>
    <tr>
        <td>Entre 30-35 anos</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->perfil['de30a35'] }}</span></td>
    </tr>
    <tr>
        <td>Homens</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->perfil['homens'] }}</span></td>
    </tr>
    <tr>
        <td>Mulheres</td>
        <td align="right"><span class="badge bg-primary">{{ $formulario->perfil['mulheres'] }}</span></td>
    </tr>
</table>