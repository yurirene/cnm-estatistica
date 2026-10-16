<table class="cartoes">
    <tr>
        @foreach($cartoes as $cartao)
            <td class="tom-{{ $cartao['tom'] }}">
                <div class="rotulo">{{ $cartao['rotulo'] }}</div>
                <div class="valor">{{ $cartao['valor'] }}</div>
            </td>
        @endforeach
    </tr>
</table>
