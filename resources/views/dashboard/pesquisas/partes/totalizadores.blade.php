@foreach($totalizadores as $totalizador)
    <div class="col-lg-4 col-xl-3 col-md-6 mt-3 p-3">
        <div class="card shadow  h-100">
            <div class="card-body">
                <h4>Item: {{$totalizador['campo']}}</h4>
                @if(isset($totalizador['valores']))
                    @foreach($totalizador['valores'] as $valor)
                    <h6>{{$valor['label']}}: {{$valor['valor']}}</h6>
                    @endforeach
                @else
                <h5>Sem dados</h5>
                @endif

            </div>
        </div>
    </div>
@endforeach