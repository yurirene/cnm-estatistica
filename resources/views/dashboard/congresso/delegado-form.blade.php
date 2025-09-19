@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Adicionar Delegado'
])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Adicionar Delegado - {{ $reuniaoModel->tipo }} {{ $reuniaoModel->ano }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.congresso.delegado.store', $reuniaoModel->id) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome">Nome <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                           id="nome" name="nome" value="{{ old('nome') }}" required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cpf">CPF <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('cpf') is-invalid @enderror"
                                           id="cpf" name="cpf" value="{{ old('cpf') }}" required>
                                    @error('cpf')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefone">Telefone</label>
                                    <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                           id="telefone" name="telefone" value="{{ old('telefone') }}">
                                    @error('telefone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                               id="suplente" name="suplente" value="1"
                                               {{ old('suplente') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="suplente">
                                            Suplente
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!$reuniaoModel->isCongressoNacional())
                        <div class="form-group">
                            <label for="tipo_instancia">Tipo de Instância <span class="text-danger">*</span></label>
                            <select class="form-control @error('tipo_instancia') is-invalid @enderror"
                                    id="tipo_instancia" name="tipo_instancia" required>
                                <option value="">Selecione o tipo</option>
                                @if($reuniaoModel->sinodal_id)
                                    <option value="sinodal" {{ old('tipo_instancia') == 'sinodal' ? 'selected' : '' }}>Sinodal</option>
                                @endif
                                @if($reuniaoModel->federacao_id)
                                    <option value="federacao" {{ old('tipo_instancia') == 'federacao' ? 'selected' : '' }}>Federação</option>
                                @endif
                                @if($reuniaoModel->local_id)
                                    <option value="local" {{ old('tipo_instancia') == 'local' ? 'selected' : '' }}>Local</option>
                                @endif
                            </select>
                            @error('tipo_instancia')
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
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Este é um Congresso Nacional. Os delegados serão identificados automaticamente.
                        </div>
                        @endif

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Adicionar Delegado
                            </button>
                            <a href="{{ route('dashboard.congresso.show', $reuniaoModel->id) }}" class="btn btn-secondary">
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
    $('#tipo_instancia').change(function() {
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
    $('#tipo_instancia').trigger('change');
});
</script>
@endpush
