<div class="row mb-3 mt-3">
    <div class="accordion" id="diretoriaButton">
        <div class="card">
            <div class="card-header p-0" id="diretoria-unico">
                <button
                    style="font-size:1rem; color: #525f7f; font-weight: 400; text-decoration:none;"
                    class="btn btn-link btn-block text-left"
                    type="button"
                    data-toggle="collapse"
                    data-target="#diretoria"
                    aria-expanded="true"
                    aria-controls="diretoria"
                >
                    Diretoria
                </button>
            </div>

            <div id="diretoria" class="collapse" aria-labelledby="diretoria-unico" data-parent="#diretoria-unico">
                <div class="card-body">
                    <div class="row mt-3">
                    @foreach($campos as $key => $valor)
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    {{ $valor['titulo'] }}
                                </div>
                                <div class="card-body p-1">
                                    <img class="img-fluid" src="/img/team-1.jpg" />
                                </div>

                                <div class="card-footer d-flex justify-content-between p-1">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <button
                                                data-cargo="{{ $valor['cargo'] }}"
                                                data-sinodalid="{{ $sinodalId }}"
                                                class="btn btn-outline-warning p-0 px-2"
                                                type="button"
                                            >
                                                <em class="fas fa-camera"></em>
                                            </button>
                                        </div>
                                        {!! Form::text(
                                            $valor['cargo'],
                                            $valor['nome'],
                                            [
                                                'class' => 'px-2 form-control',
                                                'id' => "campo_{$key}"
                                            ]
                                        ) !!}
                                        <div class="input-group-append">
                                            <button
                                                data-id="{{"campo_{$key}"}}"
                                                data-cargo="{{$valor['cargo']}}"
                                                data-sinodalid="{{ $sinodal_id }}"
                                                class="btn btn-outline-success update-cargo p-0 px-2"
                                                type="button"
                                            >
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </div>
                                    </div>
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
