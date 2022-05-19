<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Navegador - Unimed Fama')</title>
    <!-- Favicon -->
    <link href="{{ asset('argon') }}/img/brand/favicon.png" rel="icon" type="image/png">
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

    <style>
        .dataTables_filter {
            float: right;
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
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #5e72e4;
            color: white;
            font-size: 0.8rem;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
        }

        @media screen and (min-width: 1024px) {
            .modal-area-orcamentaria {
                max-width: 1000px;
                margin: auto;
            }
        }

        @media screen and (min-width: 1920px) {
            .modal-area-orcamentaria {
                max-width: 1850px;
                margin: auto;
            }
        }

    </style>
</head>

<body class="{{ $class ?? '' }}">
    {{-- @auth()
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        @include('layouts.navbars.sidebar')
    @endauth --}}

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    @include('layouts.navbars.sidebar')


    <div class="main-content">
        @include('layouts.navbars.navbar')
        @yield('content')
    </div>

    <script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script src="/datatables/datatables.min.js"></script>
    {{-- <script src="/datatables/dataTables.buttons.min.js"></script> --}}
    {{-- <script src="/datatables/buttons.dataTables.min.js"></script> --}}
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
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


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

    <script>
        $(document).ready(function() {
            $('.isDate:not([readonly])').datepicker({
                format: "dd/mm/yyyy",
                language: "pt-BR",
                todayHighlight: true,
                orientation: "bottom auto"
            });

            $('.isMounth').datepicker({
                format: "mm/yyyy",
                language: "pt-BR",
                minViewMode: 1,
                todayHighlight: true,
                orientation: "bottom auto"
            });
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
            $('.isDateRange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            $('#contas-status').select2();

            $('#auto-status').select2();

            $(".isNumber").keydown(function(e) {
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    return;
                }
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode >
                        105)) {
                    e.preventDefault();
                }
            });

            $('.isDate').attr('autocomplete', 'off');
            $('.isDate').mask('00/00/0000');
            $('.isDateTime').attr('autocomplete', 'off');
            $('.isDateTime').mask('00/00/0000 00:00:00');
            $('.isTime').mask('00:00:00');

            $('.isDateMonth').mask('00/0000');
            $('.isCPF').mask('000.000.000-00');
            $('.isCNPJ').mask('00.000.000/0000-00');
            $('.isCNS').mask('000.0000.0000.0000');
            $('.isMoney').mask('###.###.###.#00,00', {
                reverse: true
            });
            $('.isDecimal').mask('#####,000', {
                reverse: true
            });

            $('.isPeso').mask('#00,000', {
                reverse: true
            });
            $('.isAltura').mask('000');
            $('.isTemperatura').mask('00,000');

            /* Início - Máscara para telefone com e sem o 9 */
            var SPMaskBehavior = function(val) {
                    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
                },
                spOptions = {
                    onKeyPress: function(val, e, field, options) {
                        field.mask(SPMaskBehavior.apply({}, arguments), options);
                    }
                };
            $('.isTelefone').mask(SPMaskBehavior, spOptions);
            /* Fim - Máscara para telefone com e sem o 9 */

            $("#hide-sidebar").click(function() {
                $("#sidenav-main").toggle();
                $(".main-content").toggleClass("hideme");
            });
        });

        // FUNÇÃO PARA ALTERAR A IMPRESSÃO DAS DATATABLES
        var _buildUrl = function(dt, action) {
            var url = dt.ajax.url() || '';
            var params = dt.ajax.params();
            params.action = action;

            if (url.indexOf('?') > -1) {
                return url + '&' + $.param(params);
            }

            return url + '?' + $.param(params);
        };
        DataTable.ext.buttons.print = {
            className: 'buttons-print',

            text: function(dt) {
                return '<i class="fa fa-print"></i> ' + dt.i18n('buttons.print', 'Imprimir');
            },

            action: function(e, dt, button, config) {
                var url = _buildUrl(dt, 'print');
                var imprimir = window.open(url);
                imprimir.print();
                setTimeout(function() {
                    imprimir.close();
                }, 200);
            }
        };
    </script>

    <!-- Argon JS -->
    <script src="{{ asset('argon') }}/js/argon.js?v=1.0.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"
        integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>
