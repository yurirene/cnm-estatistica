<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{ $nomeSinodal }}</title>
    <meta content="Site da Sinodal" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="/sites/modelo-1/assets/img/favicon.png" rel="icon">
    <link href="/sites/modelo-1/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="/sites/modelo-1/assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="/sites/modelo-1/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/sites/modelo-1/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/sites/modelo-1/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="/sites/modelo-1/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="/sites/modelo-1/assets/vendor/remixicon/remixicon.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="/sites/modelo-1/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/sites/modelo-1/assets/vendor/grid-gallery/style.min.css"/>
    <!-- =======================================================
        * Template Name: Bootslander - v4.9.1
        * Template URL: https://bootstrapmade.com/bootslander-free-bootstrap-landing-page-template/
        * Author: BootstrapMade.com
        * License: https://bootstrapmade.com/license/
        ======================================================== -->
</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top d-flex align-items-center header-transparent">
        <div class="container d-flex align-items-center justify-content-between">

            <div class="logo">
                <h1><a href="index.html"><span>{{ strlen($nomeSinodal) < 30 ? $nomeSinodal : $sigla }}</span></a></h1>
                <!-- Uncomment below if you prefer to use an image logo -->
                <!-- <a href="index.html"><img src="/sites/modelo-1/assets/img/logo.png" alt="" class="img-fluid"></a>-->
            </div>

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto active" href="#hero">Home</a></li>
                    <li><a class="nav-link scrollto" href="#sobre">Sobre</a></li>
                    <li><a class="nav-link scrollto" href="#federacoes">Federações</a></li>
                    <li><a class="nav-link scrollto" href="#galeria">Galeria</a></li>
                    <li><a class="nav-link scrollto" href="#diretoria">Diretoria</a></li>
                    <li class="ms-md-3"><a class="btn-get-started" target="_blank" href="https://ump.org.br">Site UMP</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->

        </div>
    </header><!-- End Header -->

    <!-- ======= Hero Section ======= -->
    <section id="hero">

        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-7 pt-5 pt-lg-0 order-2 order-lg-1 d-flex align-items-center">
                    <div data-aos="zoom-out">
                        @if (!empty($titulo))
                        <h1>
                            {{$titulo}}
                            @if(!empty($destaque))
                            <span>{{ $destaque }}</span>
                            @endif
                        </h1>
                        @endif
                        @if(!empty($subtitulo))
                        <h2>{{ $subtitulo }}</h2>
                        @endif
                        @if(!empty($linkTitulo))
                        <div class="text-center text-lg-start">
                            <a href="#sobre" class="btn-get-started scrollto">{{$linkTitulo}}</a>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4 order-1 order-lg-2 hero-img" data-aos="zoom-out">
                    <img src="/sites/modelo-1/assets/img/logo-branca.png" class="img-fluid animated" alt="">
                </div>
            </div>
        </div>

        <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 24 150 28 " preserveAspectRatio="none">
            <defs>
                <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z">
            </defs>
            <g class="wave1">
                <use xlink:href="#wave-path" x="50" y="3" fill="rgba(255,255,255, .1)">
            </g>
            <g class="wave2">
                <use xlink:href="#wave-path" x="50" y="0" fill="rgba(255,255,255, .2)">
            </g>
            <g class="wave3">
                <use xlink:href="#wave-path" x="50" y="9" fill="#fff">
            </g>
        </svg>

    </section><!-- End Hero -->

    <main id="main">

        <!-- ======= sobre Section ======= -->
        <section id="sobre" class="sobre">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-5 col-lg-6 video-box d-flex justify-content-center align-items-stretch">
                    </div>

                    <div class="col-xl-7 col-lg-6 icon-boxes d-flex flex-column align-items-stretch justify-content-center py-5 px-lg-5">
                        <h3>Sobre Nós</h3>
                        {!! $sobreNos !!}
                    </div>
                </div>

            </div>
        </section><!-- End sobre Section -->

        <!-- ======= federacoes Section ======= -->
        <section id="federacoes" class="federacoes">
            <div class="container">

                <div class="section-title" data-aos="fade-up">
                    <h2>Nossas</h2>
                    <p>Federações</p>
                </div>

                <div class="row" data-aos="fade-left">
                    @foreach($federacoes as $federacao)
                    <div class="col-lg-3 col-md-4 mt-3">
                        <div class="icon-box h-100" data-aos="zoom-in" data-aos-delay="50">
                            <i class="ri-store-line" style="color: #ffbb2c;"></i>
                            <h3>{{ $federacao}}</h3>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </section><!-- End federacoes Section -->

        <!-- ======= Counts Section ======= -->
        <section id="counts" class="counts">
            <div class="container">

                <div class="row" data-aos="fade-up">

                    <div class="col-lg-4 col-md-6">
                        <div class="count-box">
                            <i class="bi bi-collection"></i>
                            <span data-purecounter-start="0" data-purecounter-end="{{$totalizador['federacao']}}" data-purecounter-duration="1"
                                class="purecounter"></span>
                            <p>Federações</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mt-5 mt-md-0">
                        <div class="count-box">
                            <i class="bi bi-house"></i>
                            <span data-purecounter-start="0" data-purecounter-end="{{$totalizador['umps']}}" data-purecounter-duration="1"
                                class="purecounter"></span>
                            <p>UMPs Locais</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mt-5 mt-lg-0">
                        <div class="count-box">
                            <i class="bi bi-people"></i>
                            <span data-purecounter-start="0" data-purecounter-end="{{ $totalizador['socios'] }}" data-purecounter-duration="1"
                                class="purecounter"></span>
                            <p>Sócios</p>
                        </div>
                    </div>
                </div>

            </div>
        </section><!-- End Counts Section -->


        <!-- ======= galeria Section ======= -->
        <section id="galeria" class="galeria">
            <div class="container">

                <div class="section-title" data-aos="fade-up">
                    <h2>Nossas</h2>
                    <p>Fotos</p>
                </div>

                <div class="row g-0" data-aos="fade-left">
                    <div class="col-md-12">
                        <div class="gg-container">
                            <div class="gg-box">
                                @foreach ($galeria as $foto)
                                <img src="/{{ $foto }}" alt="">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- End Gallery Section -->

        <!-- ======= Testimonials Section ======= -->
        <section id="testimonials" class="testimonials">
            <div class="container">
                <h4 class="text-white text-center">Alegres na Esperança, Fortes na Fé</h4>
                <h4 class="text-white text-center">Dedicados no Amor, Unidos no Trabalho</h4>
            </div>
        </section><!-- End Testimonials Section -->

        <!-- ======= Team Section ======= -->
        <section id="diretoria" class="diretoria">
            <div class="container">

                <div class="section-title" data-aos="fade-up">
                    <h2>Nossa</h2>
                    <p>Diretoria</p>
                </div>

                <div class="row" data-aos="fade-left">
                @foreach ($diretoria as $cargo)
                    <div class="col-lg-4 mt-3 col-md-6">
                        <div class="member" data-aos="zoom-in" data-aos-delay="100">
                            <div class="pic">
                                <img src="/{{ !empty($cargo['path']) ? $cargo['path'] : 'img/team-1.jpg' }}" class="img-fluid" alt="">
                            </div>
                            <div class="member-info">
                                <h4>{{ $cargo['nome'] }}</h4>
                                <span>{{ $cargo['titulo'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                </div>

            </div>
        </section><!-- End Team Section -->


    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer">
        <div class="footer-top">
            <div class="container">

            </div>
        </div>

        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>Bootslander</span></strong>. All Rights Reserved
            </div>
            <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/bootslander-free-bootstrap-landing-page-template/ -->
                Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
            </div>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="/sites/modelo-1/assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="/sites/modelo-1/assets/vendor/aos/aos.js"></script>
    <script src="/sites/modelo-1/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/sites/modelo-1/assets/vendor/glightbox/js/glightbox.min.js"></script>

    <!-- Template Main JS File -->
    <script src="/sites/modelo-1/assets/js/main.js"></script>
    <script type="text/javascript" src="/sites/modelo-1/assets/vendor/grid-gallery/script.min.js"></script>

</body>

</html>
