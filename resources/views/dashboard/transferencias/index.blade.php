@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Transferências',
    'url_tutorial' => 'https://www.youtube.com/embed/hIza8973bBA?si=-bSuLjT93u8MNCa2'
])
    
<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Transferências de {{ request()->filled('ump') ? 'UMPs' : 'Federações' }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="button-group">
                                <a href="{{ route('dashboard.transferencias.index', ['ump' => true]) }}" class="btn btn-primary">Transferir UMP</a>
                                @if ($showFederacao)
                                <a href="{{ route('dashboard.transferencias.index') }}" class="btn btn-primary">Transferir Federação</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        {!! $dataTable->table() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_transferir" tabindex="-1" aria-labelledby="modal_transferirLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_transferirLabel">Transferir</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>Transferir 
                    <span id="federacao_nome"></span> 
                    de: <span id="origem_nome"></span>
                </h4>
                <form action="{{ $rotaUpdate }}" method="POST">
                    @csrf
                    <input type="hidden" name="instancia_id" id="instancia_id">
                    @if (!request()->filled('ump'))
                    <div class="form-group">
                        <label for="sinodal_destino_id">Sinodal Destino</label>
                        <select name="sinodal_destino_id" id="sinodal_destino_id" class="form-control isSelect2Modal" required>
                            <option value="" disabled selected>Selecione uma sinodal</option>
                            @foreach ($sinodais as $key => $sinodal)
                                <option value="{{ $key }}">{{ $sinodal }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div class="form-group">
                        <label for="federacao_destino_id">Federação Destino</label>
                        <select name="federacao_destino_id" id="federacao_destino_id" class="form-control isSelect2Modal" required>
                            <option value="" disabled selected>Selecione uma federação</option>
                            @foreach ($federacoes as $key => $federacao)
                                <option value="{{ $key }}">{{ $federacao }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <button type="submit" class="btn btn-primary">Transferir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
{!! $dataTable->scripts() !!}

<script>
    $('#modal_transferir').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        $('#instancia_id').val(id)
        $('#federacao_nome').text(button.data('nome'))
        $('#origem_nome').text(button.data('origem'))
    });
    $('.isSelect2Modal').select2({
        placeholder: 'Selecione um destino',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modal_transferir')
    });
</script>
@endpush