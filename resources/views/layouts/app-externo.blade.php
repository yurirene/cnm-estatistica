<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'CNM')</title>
    <!-- Favicon -->
    <link href="{{ asset('argon') }}/img/favicon.ico" rel="icon" type="image/png">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Extra details for Live View on GitHub Pages -->

    <!-- Icons -->
    <link href="{{ asset('argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
    <link href="{{ asset('argon') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <!-- Argon CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link type="text/css" href="{{ asset('argon') }}/css/argon.css?v=1.0.1" rel="stylesheet">
    <link rel="stylesheet" href="/css/custom.css">

    <link rel="stylesheet" href="/datatables/datatables.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <style>
        @media print{
            body {
                background-color: white;
            }
            .badge {
                background-color: white !important;
                color: black !important;
                min-width: 40px;
            }
            /* @page {
               size: A4 landscape;
            } */
        }
        @media (max-width: 767.98px) {
            .fc .fc-toolbar.fc-header-toolbar {
                font-size: 10px;
                display: block;
                text-align: center;
            }

            .fc-header-toolbar .fc-toolbar-chunk {
                display: block;
            }
            .fc-today-button, .fc-prev-button, .fc-next-button {
                padding: 5px;
            }
            .fc-col-header-cell-cushion  {
                font-size: 10px;
            }
        }
        .dataTables_filter {
            float: right;
        }

        .bg-active {
            background: #5e72e4;
            color: white !important;
            border-radius: 0 2em 2em 0;
        }
        .bg-active i {
            color: white !important;
        }

        .paginate_button.page-item.previous a,
        .paginate_button.page-item.next a {
            width: 100px;
            margin-right: 20px;
            margin-left: 20px;
            border-radius: 10px !important;
        }

        .dataTables_processing.card {
            background-color: #fff;
            box-shadow: 0px 2px 6px 0px rgb(0 0 0 / 20%);
            z-index: 999;
        }

        .dataTables_filter,
        .dataTables_info {
            font-size: 0.8rem;
        }

        .dataTables_info {
            float: left;
        }

        .main-content.hideme {
            margin-left: 0px !important;
        }

        .custom-acordion {
            font-family: inherit;
            font-weight: 600;
            color: #32325d;
            font-size: .8125rem;
        }

        .accordion-item {
            border: 1px solid rgba(0, 0, 0, .05) !important;
        }
        .select2-container--default .select2-selection--multiple {

            min-height: calc(2.60rem + 2px);
            border-color: #9ba4d6;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #5e72e4;
            color: white;
            font-size: 0.875rem;
            line-height: 2rem;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
        }

        .select2-selection__rendered {
            line-height: 41px !important;
        }
        .select2-container .select2-selection--single {
            height: 45px !important;
        }
        .select2-selection__arrow {
            height: 44px !important;
        }
    </style>
</head>

<body class="{{ $class ?? '' }}">
    @if(!isset($export))
    <div class="main-content">
        @include('layouts.navbars.navs.guest')
    </div>
    @endif
    @yield('content')

    <script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        $(function(){

            var url = window.location.pathname,
                urlRegExp = new RegExp(url.replace(/\/$/,'') + "$");
                // now grab every link from the navigation
                $('.navbar-nav a').each(function(){
                    // and test its normalized href against the url pathname regexp
                    if(urlRegExp.test(this.href.replace(/\/$/,''))){
                        $(this).addClass('active bg-active');
                        if ($(this).find('i').length > 0) {
                            $(this).find('i').addClass('text-primary');
                        } else {
                            $(this).addClass('text-primary');
                        }

                        if ($(this).parents().closest('.nav-item').length > 1) {
                            console.log($(this).parents().closest('.nav-item'));
                            $(this).parents().closest('.nav-item').first().find('a').first().addClass('active text-primary');
                        }
                    }
                });

        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script src="/datatables/datatables.min.js"></script>

    <script src="/js/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pt-BR.min.js"
        integrity="sha512-mVkLPLQVfOWLRlC2ZJuyX5+0XrTlbW2cyAwyqgPkLGxhoaHNSWesYMlcUjX8X+k45YB8q90s88O7sos86636NQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/vendor/datatables/buttons.server-side.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css"
        integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script src="https://code.highcharts.com/maps/highmaps.js"></script>
    <script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
    <script src="/js/arquivo-mapa-regiao.js"></script>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('js')

    <!-- Argon JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"
        integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @if(session()->has('mensagem') && session('mensagem')['status'] == true)
    <script>
        iziToast.show({
            title: 'Sucesso!',
            message: '{{session("mensagem")["texto"]}}',
            position: 'topRight',
        });
    </script>
    @endif

    @if(session()->has('mensagem') && session('mensagem')['status'] == false)
    <script>
        iziToast.show({
            title: 'Erro!',
            message: '{{session("mensagem")["texto"]}}',
            position: 'topRight',
        });
    </script>
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
        <script>
            iziToast.show({
                title: 'Erro!',
                message: '{{$error}}',
                position: 'topRight',
            });
        </script>
        @endforeach
    @endif

    @stack('script')
</body>

</html>
