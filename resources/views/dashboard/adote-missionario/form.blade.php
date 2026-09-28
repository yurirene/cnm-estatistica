@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => isset($missionario) ? 'Editar Missionário' : 'Novo Missionário'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Formulário</h3>
                        </div>
                        <div class="col text-right">
                            <a href="{{ route('dashboard.adote-missionario.index') }}" class="btn btn-secondary btn-sm">
                                Voltar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($missionario) && $missionario->estaAdotado())
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Adotado por:</strong>
                                {{ $missionario->adotante_tipo }} — {{ $missionario->adotante_nome }}
                                @if($missionario->adotado_em)
                                    <span class="text-muted">({{ $missionario->adotado_em->format('d/m/Y H:i') }})</span>
                                @endif
                            </div>
                            <button type="button" class="btn btn-warning btn-sm"
                                onclick="alertConfirmar('{{ route('dashboard.adote-missionario.desfazer', $missionario->id) }}', 'Deseja desfazer a adoção deste missionário?')">
                                Desfazer adoção
                            </button>
                        </div>
                    @endif

                    @if(isset($missionario))
                        {!! Form::model($missionario, [
                            'route' => ['dashboard.adote-missionario.update', $missionario->id],
                            'method' => 'PUT',
                            'files' => true,
                        ]) !!}
                    @else
                        {!! Form::open([
                            'method' => 'POST',
                            'route' => 'dashboard.adote-missionario.store',
                            'files' => true,
                        ]) !!}
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('nome') ? ' has-error' : '' }}">
                                {!! Form::label('nome', 'Nome') !!}
                                {!! Form::text('nome', null, ['class' => 'form-control', 'required' => true]) !!}
                                <small class="text-danger">{{ $errors->first('nome') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group{{ $errors->has('pais') ? ' has-error' : '' }}">
                                {!! Form::label('pais', 'País') !!}
                                {!! Form::text('pais', null, ['class' => 'form-control', 'required' => true]) !!}
                                <small class="text-danger">{{ $errors->first('pais') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('regiao_id') ? ' has-error' : '' }}">
                                {!! Form::label('regiao_id', 'Região') !!}
                                {!! Form::select('regiao_id', $regioes, null, [
                                    'class' => 'form-control isSelect2',
                                    'id' => 'regiao_id',
                                    'placeholder' => 'Selecione',
                                    'required' => true,
                                ]) !!}
                                <small class="text-danger">{{ $errors->first('regiao_id') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('estado_id') ? ' has-error' : '' }}">
                                {!! Form::label('estado_id', 'UF') !!}
                                {!! Form::select('estado_id', $estados, null, [
                                    'class' => 'form-control isSelect2',
                                    'id' => 'estado_id',
                                    'placeholder' => 'Selecione a região primeiro',
                                    'required' => true,
                                ]) !!}
                                <small class="text-danger">{{ $errors->first('estado_id') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('cidade') ? ' has-error' : '' }}">
                                {!! Form::label('cidade', 'Cidade') !!}
                                {!! Form::text('cidade', null, ['class' => 'form-control', 'required' => true]) !!}
                                <small class="text-danger">{{ $errors->first('cidade') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('whatsapp') ? ' has-error' : '' }}">
                                {!! Form::label('whatsapp', 'WhatsApp') !!}
                                {!! Form::text('whatsapp', null, ['class' => 'form-control']) !!}
                                <small class="text-danger">{{ $errors->first('whatsapp') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                {!! Form::label('email', 'E-mail') !!}
                                {!! Form::email('email', null, ['class' => 'form-control']) !!}
                                <small class="text-danger">{{ $errors->first('email') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group{{ $errors->has('foto') ? ' has-error' : '' }}">
                                {!! Form::label('foto', 'Foto') !!}
                                {!! Form::file('foto', ['class' => 'form-control-file', 'accept' => 'image/*']) !!}
                                <small class="text-danger">{{ $errors->first('foto') }}</small>
                            </div>
                            @if(isset($missionario) && $missionario->foto_url)
                                <div class="mt-2">
                                    <img src="{{ $missionario->foto_url }}" alt="Foto" class="img-thumbnail" style="max-height: 120px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('outras_informacoes') ? ' has-error' : '' }}">
                        {!! Form::label('outras_informacoes', 'Outras informações') !!}
                        {!! Form::textarea('outras_informacoes', null, ['class' => 'form-control', 'rows' => 4]) !!}
                        <small class="text-danger">{{ $errors->first('outras_informacoes') }}</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Salvar</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    (function () {
        var estadosPorRegiao = @json($estadosPorRegiao);
        var estadoSelecionado = @json(old('estado_id', isset($missionario) ? $missionario->estado_id : null));

        function preencherEstados(regiaoId, selecionado) {
            var $estado = $('#estado_id');
            $estado.empty().append($('<option>', { value: '', text: 'Selecione' }));

            var lista = estadosPorRegiao[regiaoId] || {};
            Object.keys(lista).forEach(function (id) {
                $estado.append($('<option>', {
                    value: id,
                    text: lista[id],
                    selected: String(id) === String(selecionado)
                }));
            });

            if ($estado.hasClass('select2-hidden-accessible')) {
                $estado.trigger('change.select2');
            }
        }

        $('#regiao_id').on('change', function () {
            preencherEstados($(this).val(), null);
        });

        if ($('#regiao_id').val()) {
            preencherEstados($('#regiao_id').val(), estadoSelecionado);
        }
    })();
</script>
@endpush
