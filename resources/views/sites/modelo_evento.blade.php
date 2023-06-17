<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>{{$sigla}} - Evento</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css"
            rel="stylesheet" type="text/css"
        />
        <!-- Google fonts-->
        <link
            href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
            rel="stylesheet">
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="/sites/evento/css/styles.css" rel="stylesheet" />

        <style>
            header.masthead {
                position: relative;
                background: url("/{{$evento->path_arte_1}}") no-repeat;
                background-size: cover !important;
                min-height: 500px;
                padding-top: 8rem;
            }

            @media (max-width: 1140px) {
                header.masthead {
                    background-size: cover !important;
                    background: url("/{{$evento->path_arte_1}}") no-repeat center center;
                    min-height: 300px !important;
                    padding-top: 6rem;
                }
            }
            @media (max-width: 768px) {
                header.masthead {
                    background-size: cover !important;
                    background: url("/{{$evento->path_arte_1}}") no-repeat center center;
                    min-height: auto !important;
                    padding-top: 6rem;
                }
            }
            .arredondado {
                border-radius: 25px;
            }

        </style>
    </head>
    <body>
        <!-- Navigation-->
        <nav class="navbar navbar-light bg-white static-top">
            <div class="container">
                <a class="navbar-brand" href="#!">{{$sigla}} - Evento</a>
                <a class="btn btn-outline-success arredondado"
                    href="{{route('meusite.index', $sigla)}}"
                >
                    Voltar ao Site
                </a>
            </div>
        </nav>
        <!-- Masthead-->
        <header class="masthead">
        </header>
        <!-- Icons Grid-->
        <section class="features-icons bg-light">
            <div class="container">
                <h2 class="mb-4 text-center">Informações</h2>
                <div class="row">
                    <div class="col-lg-8">
                        {!! $evento->descricao !!}
                    </div>

                </div>
            </div>
        </section>


        <!-- Call to Action-->
        <section class="call-to-action text-center bg-dark text-white" id="signup">
            <div class="container position-relative">
                <div class="row justify-content-center">
                    <div class="col-xl-6">
                        <h2 class="mb-4 mt-5">Inscrição</h2>
                        <form action="{{route('meusite.evento.inscricao', $sigla)}}"
                            class="form-subscribe" id="contactFormFooter" method="POST"
                        >
                            @csrf()
                            @foreach ($evento->form as $input)
                                <div class="row mb-3">
                                    <div class="col text-start">
                                        <label>{{ ucfirst($input['campo']) }}</label>
                                        {!! $input['input'] !!}
                                    </div>
                                </div>
                            @endforeach
                            <div class="mb-3">
                                <button class="btn btn-primary btn-lg "
                                    id="submitButton" type="submit"
                                >
                                    Confirmar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- Footer-->

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

        <script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>
        <script src="/js/jquery.mask.min.js"></script>
        <script>
            $(document).ready(() => {

                $("img[src*=data\\:image]").addClass("img-fluid");
            })
        </script>
    </body>
</html>
