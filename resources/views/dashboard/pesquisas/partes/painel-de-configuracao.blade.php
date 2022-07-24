{!! Form::model($configuracoes, ['url' => route('dashboard.pesquisas.configuracoes-update', $pesquisa->id), 'method' => 'PUT', 'class' => 'form-horizontal']) !!}
<div class="row">
@foreach($configuracoes->configuracao as $campo => $configuracao)
    <div class="col-lg-4 col-xl-3 col-md-6 mt-3 p-3">
        <div class="card shadow  h-100">
            <div class="card-body">
                <div class="form-group">
                    {!! Form::label('campo', 'Campo') !!}
                    {!! Form::text('campo', $configuracao['label'], ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    <small>{{ $configuracao['campo'] }}</small>
                </div>
                
                <div class="form-group{{ $errors->has('configuracao[][tipo_grafico]') ? ' has-error' : '' }}">
                    {!! Form::label('configuracao[' . $campo . '][tipo_grafico]', 'Tipo de Gráfico') !!}
                    {!! Form::select('configuracao[' . $campo . '][tipo_grafico]', $tipos_graficos, $configuracao['tipo_grafico'], ['id' => 'configuracao[' . $campo . '][tipo_grafico]', 'class' => 'form-control']) !!}
                    <small class="text-danger">{{ $errors->first('configuracao[][tipo_grafico]') }}</small>
                </div>
                
                <div class="form-group{{ $errors->has('configuracao[][tipo_dado]') ? ' has-error' : '' }}">
                    {!! Form::label('configuracao[' . $campo . '][tipo_dado]', 'Representação do Dado') !!}
                    {!! Form::select('configuracao[' . $campo . '][tipo_dado]', $tipos_dados, $configuracao['tipo_dado'], ['id' => 'configuracao[' . $campo . '][tipo_dado]', 'class' => 'form-control']) !!}
                    <small class="text-danger">{{ $errors->first('configuracao[][tipo_dado]') }}</small>
                </div>
                
                <div class="form-group">
                    <div class="checkbox{{ $errors->has('configuracao[' . $campo . '][exportar]') ? ' has-error' : '' }}">
                        <label for="configuracao[' . $campo . '][exportar]">
                        {!! Form::checkbox('configuracao[' . $campo . '][exportar]', '1', null, ['id' => 'configuracao[' . $campo . '][exportar]']) !!} Exportar
                        </label>
                    </div>
                    <small class="text-danger">{{ $errors->first('configuracao[' . $campo . '][exportar]') }}</small>
                </div>
            </div>
        </div>
    </div>
  
@endforeach
</div>

<div class="mt-3">
    {!! Form::submit("Salvar", ['class' => 'btn btn-success']) !!}
    <a href="{{ route('dashboard.pesquisas.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>

</div>
{!! Form::close() !!}