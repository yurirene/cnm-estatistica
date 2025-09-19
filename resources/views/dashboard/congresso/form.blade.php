@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => isset($reuniao) ? 'Editar Congresso' : 'Novo Congresso'
])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ isset($reuniao) ? 'Editar Congresso' : 'Novo Congresso' }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ isset($reuniao) ? route('dashboard.congresso.update', $reuniao->id) : route('dashboard.congresso.store') }}" method="POST">
                        @csrf
                        @if(isset($reuniao))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ano">Ano <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ano') is-invalid @enderror"
                                           id="ano" name="ano" value="{{ old('ano', $reuniao->ano ?? '') }}" required>
                                    @error('ano')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="local">Local <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('local') is-invalid @enderror"
                                           id="local" name="local" value="{{ old('local', $reuniao->local ?? '') }}" required>
                                    @error('local')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror"
                                      id="descricao" name="descricao" rows="3">{{ old('descricao', $reuniao->descricao ?? '') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="data_inicio">Data de Início</label>
                                    <input type="datetime-local" class="form-control @error('data_inicio') is-invalid @enderror"
                                           id="data_inicio" name="data_inicio"
                                           value="{{ old('data_inicio', $reuniao->data_inicio ? $reuniao->data_inicio->format('Y-m-d\TH:i') : '') }}">
                                    @error('data_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="data_fim">Data de Fim</label>
                                    <input type="datetime-local" class="form-control @error('data_fim') is-invalid @enderror"
                                           id="data_fim" name="data_fim"
                                           value="{{ old('data_fim', $reuniao->data_fim ? $reuniao->data_fim->format('Y-m-d\TH:i') : '') }}">
                                    @error('data_fim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if(!isset($reuniao))
                        <div class="form-group">
                            <label for="tipo_congresso">Tipo de Congresso <span class="text-danger">*</span></label>
                            <select class="form-control @error('tipo_congresso') is-invalid @enderror"
                                    id="tipo_congresso" name="tipo_congresso" required>
                                <option value="">Selecione o tipo</option>
                                <option value="sinodal" {{ old('tipo_congresso') == 'sinodal' ? 'selected' : '' }}>Congresso Sinodal</option>
                                <option value="federacao" {{ old('tipo_congresso') == 'federacao' ? 'selected' : '' }}>Congresso de Federação</option>
                                <option value="local" {{ old('tipo_congresso') == 'local' ? 'selected' : '' }}>Congresso Local</option>
                            </select>
                            @error('tipo_congresso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" id="sinodal-group" style="display: none;">
                            <label for="sinodal_id">Sinodal <span class="text-danger">*</span></label>
                            <select class="form-control @error('sinodal_id') is-invalid @enderror"
                                    id="sinodal_id" name="sinodal_id">
                                <option value="">Selecione a sinodal</option>
                                @foreach($sinodais as $sinodal)
                                    <option value="{{ $sinodal->id }}" {{ old('sinodal_id') == $sinodal->id ? 'selected' : '' }}>
                                        {{ $sinodal->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sinodal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" id="federacao-group" style="display: none;">
                            <label for="federacao_id">Federação <span class="text-danger">*</span></label>
                            <select class="form-control @error('federacao_id') is-invalid @enderror"
                                    id="federacao_id" name="federacao_id">
                                <option value="">Selecione a federação</option>
                                @foreach($federacoes as $federacao)
                                    <option value="{{ $federacao->id }}" {{ old('federacao_id') == $federacao->id ? 'selected' : '' }}>
                                        {{ $federacao->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('federacao_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" id="local-group" style="display: none;">
                            <label for="local_id">Local <span class="text-danger">*</span></label>
                            <select class="form-control @error('local_id') is-invalid @enderror"
                                    id="local_id" name="local_id">
                                <option value="">Selecione o local</option>
                                @foreach($locais as $local)
                                    <option value="{{ $local->id }}" {{ old('local_id') == $local->id ? 'selected' : '' }}>
                                        {{ $local->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('local_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                               id="diretoria" name="diretoria" value="1"
                                               {{ old('diretoria', $reuniao->diretoria ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="diretoria">
                                            Requer Diretoria
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                               id="relatorio_estatistico" name="relatorio_estatistico" value="1"
                                               {{ old('relatorio_estatistico', $reuniao->relatorio_estatistico ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="relatorio_estatistico">
                                            Requer Relatório Estatístico
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(isset($reuniao))
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                               id="aberto" name="aberto" value="1"
                                               {{ old('aberto', $reuniao->aberto ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="aberto">
                                            Documentos Abertos
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="status" name="status">
                                        <option value="0" {{ old('status', $reuniao->status ?? 1) == 0 ? 'selected' : '' }}>Inativo</option>
                                        <option value="1" {{ old('status', $reuniao->status ?? 1) == 1 ? 'selected' : '' }}>Ativo</option>
                                        <option value="2" {{ old('status', $reuniao->status ?? 1) == 2 ? 'selected' : '' }}>Encerrado</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($reuniao) ? 'Atualizar' : 'Salvar' }}
                            </button>
                            <a href="{{ route('dashboard.congresso.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('#tipo_congresso').change(function() {
        var tipo = $(this).val();

        // Esconder todos os grupos
        $('#sinodal-group, #federacao-group, #local-group').hide();
        $('#sinodal_id, #federacao_id, #local_id').prop('required', false);

        // Mostrar o grupo correspondente
        if (tipo === 'sinodal') {
            $('#sinodal-group').show();
            $('#sinodal_id').prop('required', true);
        } else if (tipo === 'federacao') {
            $('#federacao-group').show();
            $('#federacao_id').prop('required', true);
        } else if (tipo === 'local') {
            $('#local-group').show();
            $('#local_id').prop('required', true);
        }
    });

    // Trigger change se já tiver valor selecionado
    $('#tipo_congresso').trigger('change');
});
</script>
@endpush
