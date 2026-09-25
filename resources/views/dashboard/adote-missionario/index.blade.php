@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Adote um Missionário'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-3">
                            <h3 class="mb-0">Cadastro e acompanhamento</h3>
                        </div>
                        <div class="col-md-9 text-right">
                            <button type="button" class="btn btn-info"
                                onclick="alertConfirmar('{{ route('dashboard.adote-missionario.sincronizar-jmn') }}', 'Sincronizar missionários da JMN? Os dados existentes serão atualizados sem alterar as adoções.')">
                                <i class="fas fa-sync-alt"></i> Sincronizar JMN
                            </button>
                            <button type="button" class="btn btn-info"
                                onclick="alertConfirmar('{{ route('dashboard.adote-missionario.sincronizar-apmt') }}', 'Sincronizar missionários da APMT? Os dados existentes serão atualizados sem alterar as adoções.')">
                                <i class="fas fa-sync-alt"></i> Sincronizar APMT
                            </button>
                            <button type="button" class="btn btn-warning"
                                onclick="alertConfirmar('{{ route('dashboard.adote-missionario.remover-vinculos') }}', 'Remover TODOS os vínculos de adoção? Esta ação libera todos os missionários novamente.')">
                                <i class="fas fa-unlink"></i> Remover todos os vínculos
                            </button>
                            <a href="{{ route('dashboard.adote-missionario.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Novo Missionário
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <select id="filtro-regiao" class="form-control form-control-sm">
                                <option value="">Região: todas</option>
                                @foreach($regioes as $id => $nome)
                                    <option value="{{ $id }}">{{ $nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select id="filtro-agencia" class="form-control form-control-sm">
                                <option value="">Agência: todas</option>
                                <option value="jmn">JMN</option>
                                <option value="apmt">APMT</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        {!! $dataTable->table(['class' => 'table w-100']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
{!! $dataTable->scripts() !!}
<script>
    $(function () {
        $('#filtro-regiao, #filtro-agencia').on('change', function () {
            $('#missionarios-table').DataTable().ajax.reload();
        });
    });
</script>
@endpush
