<table>
    <thead>
    <tr>
        @foreach($cabecalho[0] as $cab)
        <th>{{ $cab }}</th>
        @endforeach
    </tr>
    <tr>
        @foreach($cabecalho[1] as $coluna)
        <th>{{ $coluna }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($dados as $dado)
        <tr>
            @foreach($coluna_por_grupo as $coluna => $campos)
                @if(!is_array($campos))
                    <td>{{ $dado[$campos] }}</td>
                    @continue
                @endif
                @foreach($campos as $subcoluna)
                    @if($subcoluna == 'regiao')
                    <td>{{ $dado[$coluna][$subcoluna]['nome'] }}</td>
                    @else
                    <td>{{ $dado[$coluna][$subcoluna] }}</td>
                    @endif
                @endforeach
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
