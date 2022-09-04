@if ($parametro['tipo'] == 'switch')
<div class="col-md-3">

    <span>{{ $parametro['label'] }}</span><br>
    <input type="checkbox"
    class="parametro"
    data-toggle="toggle"
    data-onstyle="success"
    data-on="Ativado"
    data-id="{{$parametro['id']}}"
    data-off="Desativado"
    name="{{ $parametro['nome']}}"
    id="{{ $parametro['nome']}}"
    {{$parametro['valor'] == 'SIM' ? 'checked' : ''}} >
</div>
@endif
@if ($parametro['tipo'] == 'text')
<div class="col-md-3">
    <label>{{ $parametro['label'] }}</label>
    <div class="input-group mb-3">
        <input class="form-control parametro-valor" type="text" value="{{ $parametro['valor'] }}" data-id="{{ $parametro['id'] }}">
        <div class="input-group-append">
            <span class="input-group-text">
                <button class="btn btn-success btn-parametro m-0 py-0 px-2" >
                    <i class="fas fa-save"></i>
                </button>
            </span>
        </div>
    </div>
</div>
@endif
