@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Tutoriais'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Tutoriais</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4  col-sm-12 p-2">
                            <div class="card shadow">
                                <div class="card-header">
                                    Formulário Estatístico - UMP Local
                                </div>
                                <div class="card-body">

                                    <iframe
                                        width="100%"
                                        height="315"
                                        src="https://www.youtube.com/embed/UrbtSnbrk9k"
                                        title="YouTube video player"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 p-2">
                            <div class="card shadow ">
                                <div class="card-header">
                                    Formulário Estatístico - Federação
                                </div>
                                <div class="card-body">


                                    <iframe
                                        width="100%"
                                        height="315"
                                        src="https://www.youtube.com/embed/6eBoaLOhLFQ"
                                        title="YouTube video player"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 p-2">
                            <div class="card shadow">
                                <div class="card-header">
                                    Formulário Estatístico - Sinodal
                                </div>
                                <div class="card-body">
                                    <iframe
                                        width="100%"
                                        height="315"
                                        src="https://www.youtube.com/embed/LrhLb5opZXA"
                                        title="YouTube video player"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 p-2">
                            <div class="card shadow">
                                <div class="card-header">
                                    Cadastro de Usuários
                                </div>
                                <div class="card-body">
                                    <a href="/downloads/tutorial_usuario.pdf" class="btn btn-primary"
                                        target="_blank"
                                    >
                                        <i class="fas fa-file"></i> Visualizar
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 p-2">
                            <div class="card shadow">
                                <div class="card-header">
                                    Módulo Financeiro
                                </div>
                                <div class="card-body">
                                    <a href="/downloads/modulo_financeiro.pdf" class="btn btn-primary"
                                        target="_blank"
                                    >
                                        <i class="fas fa-file"></i> Visualizar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
