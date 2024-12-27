@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'UMPs Locais'
])
    
<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Lista de UMPs</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        {!! $dataTable->table() !!}
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<div class="modal fade" id="modal_diretoria"
    data-backdrop="static"
    data-keyboard="false"
    tabindex="-1"
    aria-labelledby="modal_diretoriaLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_diretoriaLabel">
                    Diretoria
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Informação atualizada em: <span id="dir_atualizacao"></span></h5>
                <table class="table table-striped">
                    <thead>
                        <th>Cargo</th>
                        <th>Nome</th>
                        <th>Contato</th>
                    </thead>
                    <tbody id="dir_cargos">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')

{!! $dataTable->scripts() !!}

<script>
    $('#modal_diretoria').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var dados = button.data('dados')
        Object.entries(dados.cargos).forEach(([cargo, dado]) => {
            $('#dir_cargos').append(`
                <tr>
                    <td>${cargo}</td>
                    <td>${dado.nome ?? 'Sem Registro'}</td>
                    <td>${dado.contato ?? 'Sem Registro'}</td>
                </tr>
            `)
        })
        
        $('#dir_cargos').append(`
            <tr>
                <td>Secretários de Atividades</td>
                <td colspan="2">${dados.secretarios ?? 'Sem Secretários'}</td>
            </tr>
        `)
        $('#dir_atualizacao').text(dados.atualizacao)
    });
</script>
@endpush