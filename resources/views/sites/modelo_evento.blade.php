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
        <link rel="stylesheet"
                href="/vendor/datepicker/bootstrap-datepicker.min.css"/>

        @php
        $url = $evento->path_arte_1 != 'https://placehold.co/1995x525'
            ? '/' . $evento->path_arte_1
            : $evento->path_arte_1;
        @endphp
        <style>
            header.masthead {
                position: relative;
                background-size: 100% !important;
                background: url("{{$url}}") no-repeat center center;
                min-height: 450px;
                padding-top: 8rem;
            }

            @media (width < 1140px) {
                header.masthead {
                    background-size: cover !important;
                    background: url("{{$url}}") no-repeat center center;
                    min-height: 200px !important;
                    padding-top: 6rem;
                }
            }
            @media (width < 768px) {
                header.masthead {
                    background-size: 100% !important;
                    background: url("{{$url}}") no-repeat center center;
                    min-height: 70px !important;
                    padding-top: 6rem;
                }
            }
            .arredondado {
                border-radius: 25px;
            }
            .border-bottom {
                border-bottom: 1px solid #585a5c !important;
                width: 75%;
                margin-left: 12.25%
            }
            .fot {
                background-color: #010351
            }

        </style>
    </head>
    <body>
        <!-- Navigation-->
        <nav class="navbar navbar-light bg-white static-top">
            <div class="container">
                <a class="navbar-brand" href="#!">{{$sigla}} - Evento</a>
                <a class="btn btn-outline-success btn-sm arredondado"
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

                <div class="section-title" data-aos="fade-up">
                    <h2>Mais</h2>
                    <p>Informações</p>
                </div>
                <div class="row">
                    <div class="col-lg-10 offset-md-1">
                        {!! $evento->descricao !!}
                    </div>

                </div>
            </div>
        </section>

        <!-- ======= Testimonials Section ======= -->
        <section id="testimonials" class="testimonials">
            <div class="container">
                <h4 class="text-white text-center">Alegres na Esperança, Fortes na Fé</h4>
                <h4 class="text-white text-center">Dedicados no Amor, Unidos no Trabalho</h4>
            </div>
        </section><!-- End Testimonials Section -->



        <!-- Call to Action-->
        <section class="call-to-action" id="signup" style="min-height:400px;">
            <div class="container position-relative">

                <div class="section-title mt-5" data-aos="fade-up">
                    <h2>Faça já sua</h2>
                    <p>Inscrição</p>
                </div>
                <div class="row justify-content-center text-center">
                    <div class="col-xl-6">
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
                            <div class="mb-3 mt-5">
                                <button class="btn btn-primary "
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

        <section class="fot text-white mt-5" id="signup">
            <footer class="py-3">
                <ul class="nav justify-content-center border-bottom pb-3 mb-3">
                    <li class="nav-item"><a href="https://ump.org.br" class="nav-link px-2 text-muted">Site CNM</a></li>
                    <li class="nav-item"><a href="{{route('meusite.index', $sigla)}}" class="nav-link px-2 text-muted">Site Sinodal</a></li>
                </ul>
                <p class="text-center text-muted">&copy; {{date('Y')}} União de Mocidade Presbiteriana</p>
            </footer>

        </section>
        <!-- Footer-->

        <!-- Bootstrap core JS-->
        <script src="/vendor/externo-bootstrap.bundle.min.js"></script>

        <script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>
        <script src="/js/jquery.mask.min.js"></script>
        <script src="/vendor/sweetalert.min.js"></script>

        <script src="/vendor/datepicker/bootstrap-datepicker.min.js"></script>
        <script src="/vendor/datepicker/ptbr.min.js"></script>
        <script>
            $(document).ready(() => {

                $("img[src*=data\\:image]").addClass("img-fluid");

                $('.isDate:not([readonly])').datepicker({
                    format: "dd/mm/yyyy",
                    language: "pt-BR",
                    todayHighlight: true,
                    orientation: "bottom auto"
                });
                $('input').attr('autocomplete', 'off');
                $('.isTelefone').mask('(00) 00000-0000');
            })

        </script>
        @if(session()->has('status') && session('status') == true)
            <script>
            Swal.fire({
                title: 'Sucesso!',
                text: 'Inscrição realizada com sucesso!',
                icon: 'success',
                confirmButtonText: 'Ok'
            })
            </script>
        @endif

        @if(session()->has('status') && session('status') == false)
            <script>
                Swal.fire({
                    title: 'Erro!',
                    text: 'Erro ao realizar inscrição!',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                })
            </script>
        @endif
    </body>
</html>
