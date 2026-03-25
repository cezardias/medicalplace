<!doctype html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- CSRF Token -->
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:title" content="">
        <meta property="og:type" content="">
        <meta property="og:url" content="">
        <meta property="og:image" content="">
        <meta name="theme-color" content="#fafafa">

        <link rel="manifest" href="/site.webmanifest">
        <link rel="apple-touch-icon" href="icon.png">
        <!-- Place favicon.ico in the root directory -->
        <link href="https://fonts.googleapis.com/css2?family=Archivo&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
        <link rel="stylesheet" href="{{ asset('css/jquery-ui.theme.css') }}">
        <!-- <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css"> -->
        {{--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">--}}
        <!-- <link rel="stylesheet" href="{{ asset('css/swiper.min.css') }}"> -->
        <link rel="stylesheet" href="{{ asset('assets/owl_carousel/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/bootstrap-4.5.3-dist/css/bootstrap.min.css') }}">

        <link rel="stylesheet" href="{{ asset('assets/toastr-master/build/toastr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/chosen/chosen.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
        <link rel="stylesheet" href="{{ asset('css/main.css') }}">

        @yield('style')

    </head>
    <body id="home">
        <div class="container-fluid">
            <nav class="navbar navbar-expand-lg">
                <!-- logo -->
                <a href="https://medicalplace.med.br/" class="navbar-brand" title="Voltar para o site">
                    <img src="{{ asset('img/logotipo/logo.png') }}" alt="" width="78">
                </a>
                <!-- botao expande menu -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topMenu">
                    icone
                </button>
                <div class="collapse navbar-collapse w-100" id="topMenu">
                    <ul class="navbar-nav w-100 justify-content-center">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link titulo">Agendamento de Consultório</a>
                        </li>
                        {{--
                        <li class="nav-item">
                            <a href="https://medicalplace.med.br/#quemsomos" target="_blank" class="nav-link">Quem somos</a>
                        </li>
                        <li class="nav-item">
                            <a href="https://medicalplace.med.br/#contato" target="_blank" class="nav-link">Contato</a>
                        </li>
                        --}}
                    </ul>
                    <!-- botão login -->
                    @guest
                        <div class="ml-auto">
                            <a class="btn btn-default" id="login" href="{{ route('login') }}">
                                <i class="fa fa-user-o"></i>&nbsp;&nbsp;{{ __('Login') }}
                            </a>
                        </div>
                    @else
                        <div class="ml-auto">
                            <div class="dropdown show">
                                <a class="btn btn-default-outline dropdown-toggle" href="#" role="button" id="dropdo17wnMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="#" class="rounded-circle" alt=""> &nbsp;{{ Auth::user()->name }}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item" href="{{ route('minha_conta') }}">Minha conta</a>
                                    <div class="dropdown-divider"></div>
                                    {{--
                                    <a class="dropdown-item" href="{{ route('minha_conta') }}#dados">Meus Dados</a>
                                    <a class="dropdown-item" href="{{ route('minha_conta') }}#carteira">Carteira</a>
                                    <a class="dropdown-item" href="{{ route('minha_conta') }}#historico">Histórico</a>
                                    <div class="dropdown-divider"></div>
                                    --}}
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endguest
                </div>
                <div class="float-bg">
                    <img src="{{ asset('img/logotipo/menu-bg.svg') }}" alt="" width="78">
                </div>
            </nav>

            @yield('content')

        </div>
        <script src="{{ asset('js/vendor/jquery-3.5.1.js') }}"></script>
        <script src="{{ asset('js/vendor/jquery-ui.js') }}"></script>
        <!-- <script src="https://unpkg.com/swiper/swiper-bundle.js"></script> -->

        {{--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>--}}

        <script src="{{ asset('assets/bootstrap-4.5.3-dist/js/bootstrap.bundle.min.js') }}"></script>

        <script src="{{ asset('js/vendor/modernizr-3.11.2.min.js') }}"></script>
        <script src="{{ asset('assets/jquery_mask/dist/jquery.mask.min.js') }}"></script>
        <script src="{{ asset('assets/toastr-master/build/toastr.min.js') }}"></script>
        <script src="{{ asset('assets/chosen/chosen.jquery.min.js') }}"></script>
        <script src="{{ asset('assets/owl_carousel/owl.carousel.min.js') }}"></script>

        <script src="{{ asset('js/plugins.js') }}"></script>
        <script src="{{ asset('js/main.js') }}"></script>

        @yield('javascript')

        <!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->
        <script>
            window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
            ga('create', 'UA-XXXXX-Y', 'auto'); ga('set', 'anonymizeIp', true); ga('set', 'transport', 'beacon'); ga('send', 'pageview')
        </script>
        <script src="https://www.google-analytics.com/analytics.js" async></script>

        <script>

            @if(Session::has('toastr'))

                @if (Session::get('toastr.status', 'info') == "sucesso_reserva_cancelada")
                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": true,
                        "positionClass": "toast-top-center",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "10000",
                        "hideDuration": "1000",
                        "timeOut": "10000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut",
                        "tapToDismiss": false
                    }
                    toastr["success"]("Olá, o seu agendamento foi cancelado com sucesso. <br /><br />O valor cobrado está na sua carteira digital, como crédito e poderá ser usado para futuros agendamentos.");

                @else
                    toastr.options = {
                        "closeButton": false,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": true,
                        "positionClass": "toast-bottom-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    }
                    toastr["{{ Session::get('toastr.status', 'info') }}"]("{{ Session::get('toastr.message') }}");
                @endif
            @endif

            $(function () {
                $( ".datePicker" ).datepicker({ dateFormat: 'dd/mm/yy' });
                $('[data-toggle="tooltip"]').tooltip();
                $('.cpf').mask('000.000.000-00', {reverse: true});
                $(".telefone").mask("(99) 99999-9999");
                $('.moeda').mask('#.##0,00', {reverse: true});
            })
        </script>
    </body>
</html>
