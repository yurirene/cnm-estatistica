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
                    @php
                        $valor = $dado[$campos] ?? '';
                        $valor = is_array($valor) || is_object($valor) ? json_encode($valor) : (string) $valor;
                    @endphp
                    <td>{{ $valor }}</td>
                    @continue
                @endif
                @foreach($campos as $subcoluna)
                    @if($subcoluna == 'regiao')
                    <td>{{ $dado[$coluna][$subcoluna]['nome'] ?? '' }}</td>
                    @else
                    @php
                        $valor = $dado[$coluna][$subcoluna] ?? '';
                        $valor = is_array($valor) || is_object($valor) ? json_encode($valor) : (string) $valor;
                    @endphp
                    <td>{{ $valor }}</td>
                    @endif
                @endforeach
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
