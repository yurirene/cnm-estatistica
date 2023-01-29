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

            <div id="galeria" class="collapse" aria-labelledby="galeria-unico" data-parent="#galeria-unico">
                <div class="card-body">
                    <button class="btn btn-xs btn-primary p-2">
                        <em class="fas fa-plus"></em> Adicionar
                    </button>
                    <div class="row mt-3"  style="max-height: 250px; overflow-y: auto;">
                    @foreach($fotos as $foto)
                        <div class="col-md-3 mt-3">
                            <div class="card shadow">
                                <div class="card-header text-end p-1">
                                    <em class="text-danger fas fa-trash"></em>
                                </div>
                                <div class="card-body p-1">
                                    <img class="img-fluid" src="/img/team-1.jpg" />
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
