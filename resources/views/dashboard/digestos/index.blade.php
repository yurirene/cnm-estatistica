@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Digestos'
])

@php
    $incompletos = (int) ($completude['incompletos'] ?? 0);
    $total = (int) ($completude['total'] ?? 0);
    $statusAtual = request('status');
@endphp

<div class="container-fluid mt--7 digesto-admin">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-body">
                    @if($incompletos > 0)
                        <div class="digesto-completude">
                            <div class="n">{{ $incompletos }}</div>
                            <div class="t">
                                de {{ $total }} documentos estão sem <b>Tipo</b> ou <b>Nº do documento</b>
                                preenchidos — eles aparecem incompletos na busca pública do Digesto.
                            </div>
                            <button type="button" class="btn" id="digesto-ver-incompletos">Ver incompletos</button>
                        </div>
                    @endif

                    <div class="digesto-admin-toolbar">
                        <div class="digesto-admin-toolbar-left">
                            <a href="{{ route('dashboard.digestos.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Novo Digesto
                            </a>
                            <select id="digesto-filtro-reuniao" class="form-control">
                                <option value="">Reunião: Todas</option>
                                @foreach($tipos as $id => $nome)
                                    <option value="{{ $id }}" @selected((string) request('tipo_reuniao') === (string) $id)>{{ $nome }}</option>
                                @endforeach
                            </select>
                            <select id="digesto-filtro-ano" class="form-control">
                                <option value="">Ano: Todos</option>
                                @foreach($anos as $ano)
                                    <option value="{{ $ano }}" @selected((string) request('ano') === (string) $ano)>{{ $ano }}</option>
                                @endforeach
                            </select>
                            <select id="digesto-filtro-status" class="form-control">
                                <option value="">Status: Todos</option>
                                <option value="completo" @selected($statusAtual === 'completo')>Completo</option>
                                <option value="incompleto" @selected($statusAtual === 'incompleto')>Incompleto</option>
                            </select>
                        </div>
                        <input type="search" id="digesto-busca-titulo" class="form-control digesto-tbl-search" placeholder="Pesquisar título...">
                    </div>

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
<script>
    $(document).on('preXhr.dt', '#digesto-table', function (e, settings, data) {
        data.tipo_reuniao = $('#digesto-filtro-reuniao').val();
        data.ano = $('#digesto-filtro-ano').val();
        data.status = $('#digesto-filtro-status').val();
    });
</script>
{!! $dataTable->scripts() !!}
<script>
    $(function () {
        var table = $('#digesto-table').DataTable();

        $('#digesto-filtro-reuniao, #digesto-filtro-ano, #digesto-filtro-status').on('change', function () {
            table.ajax.reload();
        });

        $('#digesto-busca-titulo').on('keyup', function () {
            table.search(this.value).draw();
        });

        $('#digesto-ver-incompletos').on('click', function () {
            $('#digesto-filtro-status').val('incompleto').trigger('change');
        });
    });
</script>
@endpush
