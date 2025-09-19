@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Novo Congresso Nacional'
])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Novo Congresso Nacional</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.congresso-nacional.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ano">Ano <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ano') is-invalid @enderror"
                                           id="ano" name="ano" value="{{ old('ano') }}" required>
                                    @error('ano')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="local">Local <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('local') is-invalid @enderror"
                                           id="local" name="local" value="{{ old('local') }}" required>
                                    @error('local')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror"
                                      id="descricao" name="descricao" rows="3">{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="data_inicio">Data de Início</label>
                                    <input type="datetime-local" class="form-control @error('data_inicio') is-invalid @enderror"
                                           id="data_inicio" name="data_inicio" value="{{ old('data_inicio') }}">
                                    @error('data_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="data_fim">Data de Fim</label>
                                    <input type="datetime-local" class="form-control @error('data_fim') is-invalid @enderror"
                                           id="data_fim" name="data_fim" value="{{ old('data_fim') }}">
                                    @error('data_fim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                               id="diretoria" name="diretoria" value="1"
                                               {{ old('diretoria') ? 'checked' : '' }}>
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
                                               {{ old('relatorio_estatistico') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="relatorio_estatistico">
                                            Requer Relatório Estatístico
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar
                            </button>
                            <a href="{{ route('dashboard.congresso-nacional.index') }}" class="btn btn-secondary">
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
