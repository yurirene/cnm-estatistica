<div class="row mb-3 mt-3">
    <div class="accordion" id="galeriaButton">
        <div class="card">
            <div class="card-header p-0" id="galeria-unico">
                <button
                    style="font-size:1rem; color: #525f7f; font-weight: 400; text-decoration:none;"
                    class="btn btn-link btn-block text-left"
                    type="button"
                    data-toggle="collapse"
                    data-target="#galeria"
                    aria-expanded="true"
                    aria-controls="galeria"
                >
                    Galeria
                </button>
            </div>

            <div id="galeria" class="collapse show active" aria-labelledby="galeria-unico" data-parent="#galeria-unico">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            {!! Form::open(
                                [
                                    'method' => 'POST',
                                    'route' => ['dashboard.apps.sites.adicionar-galeria',
                                        ['sinodal_id' => $sinodal_id]
                                    ],
                                    'files' => true
                                ])
                            !!}

                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input"
                                        id="image" aria-describedby="inputenviarimg"
                                        name="foto"
                                    >
                                    <label class="custom-file-label" for="image">Buscar Imagem</label>
                                </div>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-success" type="submit" id="inputenviarimg">Enviar</button>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div class="row mt-3"  style="max-height: 250px; overflow-y: auto;">
                    @foreach($fotos as $foto)
                        <div class="col-md-3 mt-3">
                            <div class="card shadow h-100">
                                <div class="card-header text-end p-1">
                                    <em
                                        data-id="{{ $foto['id'] }}"
                                        class="text-danger fas fa-trash delete-galeria"
                                    ></em>
                                </div>
                                <div class="card-body p-1">
                                    <img class="img-fluid" src="/{{$foto['path']}}" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('js')
<script>
const ROUTE = "{{ route('dashboard.apps.sites.remover-galeria', ['sinodal_id' => $sinodal_id, 'id' => ':id']) }}";

$('.delete-galeria').on('click', function () {
    var id = $(this).data('id');
    var url = ROUTE.replace(':id', id);

    deleteRegistro(url);
})

</script>
@endpush
