<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'CNM')</title>
    <!-- Favicon -->
    <link href="{{ asset('argon') }}/img/favicon.ico" rel="icon" type="image/png">
    <!-- Fonts -->
    {{-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet"> --}}
    <!-- Extra details for Live View on GitHub Pages -->

    <!-- Icons -->
    <link href="{{ asset('argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
    <link href="{{ asset('argon') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <!-- Argon CSS -->
    <link href="/vendor/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="{{ asset('argon') }}/css/argon.css?v=1.0.1" rel="stylesheet">
    <link rel="stylesheet" href="/css/custom.css">

    <link rel="stylesheet" href="/datatables/datatables.min.css">
    <link rel="stylesheet" href="/vendor/datepicker/bootstrap-datepicker.min.css"/>
    <link href="/vendor/select2/estilo.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="/vendor/charts/morris.css">


    <link rel="stylesheet" href="/vendor/calendario/main.min.css">
    <script src="/vendor/calendario/main.min.js"></script>

    <link rel="stylesheet" href="/vendor/font_bootstrap-icons.css">

    <style>
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
        .toggle.btn-lg {
            min-width: 140px !important;
        }
        .btn-lg, .btn-group-lg > .btn {
            padding: 0.65rem 1rem;
        }
    </style>
    <script>

    </script>
</head>

<body class="{{ $class ?? '' }}">

    <div class="overlay_init" id="overlay_init">
        <div class="overlay_init__inner">
            <div class="overlay_init__content">
                <span class="spinner"></span>
            </div>
        </div>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    @auth()
    @include('layouts.navbars.sidebar')
    @endauth

    <div class="main-content">
        @include('layouts.navbars.navbar')
        @yield('content')

        @auth
            <footer class="footer">
                <div class="copyright text-center text-muted">
                    <img class="text-center" src="/img/logos/ump.png" style="height: 60px;" alt="" />
                    Confederação Nacional de Mocidade
                </div>
            </footer>
        @endauth
    </div>
    <script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>



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
                            $(this).parents()
                                .closest('.nav-item')
                                .first()
                                .find('a')
                                .first()
                                .addClass('active text-primary');
                        }
                    }
                });

        });
    </script>

    <script src="/vendor/bootstrap.bundle.min.js"></script>

    <script src="/datatables/datatables.min.js"></script>

    <script src="/js/jquery.mask.min.js"></script>
    <script src="/vendor/datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/vendor/datepicker/ptbr.min.js"></script>
    <script src="/vendor/datatables/buttons.server-side.js"></script>
    <link rel="stylesheet" href="/vendor/iziToast/estilo.min.css" />
    <script type="text/javascript" src="/vendor/moment.min.js"></script>
    <script type="text/javascript" src="/vendor/datepicker/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="/vendor/datepicker/daterangepicker.css" />

    <script src="/vendor/select2/script.min.js"></script>


    <script src="/vendor/charts/highmaps.js"></script>
    <script src="/vendor/charts/highmaps-export.js"></script>
    <script src="/js/arquivo-mapa-regiao.js"></script>



    <script src="/vendor/charts/raphael.min.js"></script>
    <script src="/vendor/charts/morris.min.js"></script>

    <script src="/vendor/popper.min.js" crossorigin="anonymous"></script>

    <script src="/vendor/sweetalert.min.js"></script>

    <!-- SUMMERNOTE -->
    <link href="/vendor/summernote/summernote-lite.min.css" rel="stylesheet">
    <script src="/vendor/summernote/summernote-lite.min.js"></script>
    <script src="/vendor/summernote/lang/summernote-pt-BR.js"></script>
    <script src="/vendor/summernote/plugin/specialchars/summernote-ext-specialchars.js"></script>

    <script>
        function deleteRegistro(url) {
                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Deseja apagar o registro?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                })
            }
    </script>
    <script>
        (function($) {  // important!!!
            // in here it is safe to use $ for jQuery (nowhere else!)
            $('.isDate:not([readonly])').datepicker({
                format: "dd/mm/yyyy",
                language: "pt-BR",
                todayHighlight: true,
                orientation: "bottom auto"
            });
            $('.isDate').attr('autocomplete', 'off');
            $('.isDate').mask('00/00/0000');
            $('.isYear').mask('0000');
        })(jQuery)
        $('.isTelefone').mask('(99)99999-9999');
    </script>
    <script>
        $(document).ready(function() {

            $('.isDateRange').daterangepicker({
                autoUpdateInput: false,
                "locale": {
                    "format": "DD/MM/YYYY",
                    "separator": " - ",
                    "applyLabel": "Aplicar",
                    "cancelLabel": "Cancelar",
                    "fromLabel": "De",
                    "toLabel": "Até",
                    "customRangeLabel": "Custom",
                    "daysOfWeek": [
                        "D",
                        "S",
                        "T",
                        "Q",
                        "Q",
                        "S",
                        "S"
                    ],
                    "monthNames": [
                        "Janeiro",
                        "Fevereiro",
                        "Março",
                        "Abril",
                        "Maio",
                        "Junho",
                        "Julho",
                        "Agosto",
                        "Setembro",
                        "Outubro",
                        "Novembro",
                        "Dezembro"
                    ],
                    "firstDay": 0
                },
                function(start_date, end_date) {
                    this.element.val(start_date.format('DD/MM/YYYY') + ' - ' + end_date.format(
                        'DD/MM/YYYY'));
                }
            })

            $('.isDateRange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
            });

            $('.isSelect2').select2();


            $('.isMoney').mask("#.##0,00", {reverse: true});

            $("#hide-sidebar").click(function() {
                $("#sidenav-main").toggle();
                $(".main-content").toggleClass("hideme");
            });
        });

    </script>
    @stack('js')

    <script>
        (function($, DataTable) {
            $.extend(true, DataTable.defaults, {
                language: {
                    "sEmptyTable": "Nenhum registro encontrado",
                    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": "_MENU_ resultados por página",
                    "sLoadingRecords": "Carregando...",
                    "sProcessing": "Processando...",
                    "sZeroRecords": "Nenhum registro encontrado",
                    "sSearch": "Pesquisar",
                    "oPaginate": {
                        "sNext": "Próximo",
                        "sPrevious": "Anterior",
                        "sFirst": "Primeiro",
                        "sLast": "Último"
                    },
                    "oAria": {
                        "sSortAscending": ": Ordenar colunas de forma ascendente",
                        "sSortDescending": ": Ordenar colunas de forma descendente"
                    },
                    "select": {
                        "rows": {
                            "_": "Selecionado %d linhas",
                            "0": "Nenhuma linha selecionada",
                            "1": "Selecionado 1 linha"
                        }
                    },
                    "buttons": {
                        "copy": "Copiar para a área de transferência",
                        "copyTitle": "Cópia bem sucedida",
                        "copySuccess": {
                            "1": "Uma linha copiada com sucesso",
                            "_": "%d linhas copiadas com sucesso"
                        }
                    }
                }

            });

        })(jQuery, jQuery.fn.dataTable);
    </script>



    <!-- Argon JS -->
    <script src="{{ asset('argon') }}/js/argon.js?v=1.0.0"></script>
    <script src="/vendor/iziToast/iziToast.min.js"></script>
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

    <script>
        function confirmar(input)
        {
            event.preventDefault();
            var rota = $(input).attr('href');
            iziToast.error({
                timeout: 20000,
                close: false,
                overlay: true,
                displayMode: 'once',
                id: 'question',
                zindex: 999,
                title: 'Atenção',
                message: 'Você tem certeza disso?',
                position: 'center',
                buttons: [
                    ['<button><b>Sim</b></button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        window.location.href = rota;
                    }, true],
                    ['<button>Cancelar</button>', function (instance, toast) {
                        instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    }],
                ],
            });

        }
    </script>
    <script>
        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "inherit" );
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css( "overflow", "auto" );
        })
    </script>

    <script src="/vendor/chart.js"></script>


<script src="/vendor/jquery-ui.min.js"></script>
<script src="/js/form-builder.min.js"></script>
<script src="/js/form-render.min.js"></script>

<script>
    function deleteRegistro(url) {
        Swal.fire({
            title: 'Tem certeza?',
            text: "Deseja apagar o registro?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }

    function alertConfirmar(url, texto = 'Você confirma a ação?') {
        Swal.fire({
            title: 'Tem certeza?',
            text: texto,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }
</script>



<link href="/vendor/bootstrap-toggle/estilo.min.css" rel="stylesheet">
<script src="/vendor/bootstrap-toggle/script.min.js"></script>

    @stack('script')


<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            $('#overlay_init').hide();
        }, 500)
    }, false);
</script>


</body>

</html>
