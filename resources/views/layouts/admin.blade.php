<!doctype html>
<html class="no-js" lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title>Medical Place</title>


        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:title" content="">
        <meta property="og:type" content="">
        <meta property="og:url" content="">
        <meta property="og:image" content="">
        <meta name="theme-color" content="#fafafa">

        <link rel="manifest" href="site.webmanifest">
        <link rel="apple-touch-icon" href="icon.png">
        <!-- Place favicon.ico in the root directory -->
        <link href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Archivo&display=swap" rel="stylesheet">
        <!--link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous"-->
        <!--link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}"-->
        <link rel="stylesheet" href="{{ asset('assets/font-awesome-4.7.0/css/font-awesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/normalize.css') }}">
        <link rel="stylesheet" href="{{ asset('css/jquery-ui.theme.css') }}">
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css">
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('assets/toastr-master/build/toastr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/chosen/chosen.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/main.css') }}">

        @yield('style')

    </head>
    <body id="dashboard">
        <i style="cursor: pointer;" class="fa fa-bars abre-menu" id="menu-toggle-abre"></i>
        <div class="container-fluid px-0 py-0">
            <div class="d-flex" id="wrapper">
            <!-- Sidebar -->
            <div class="bg-light border-right" id="sidebar-wrapper">
                <div class="sidebar-heading">
                <img src="/img/logotipo/logo.png" alt="" width="78">
                <i style="cursor: pointer;" class="fa fa-close" id="menu-toggle"></i>
                {{--<button class="btn btn-primary" id="menu-toggle">Toggle Menu</button>--}}
                </div>
                <div class="list-group list-group-flush" id="accordion">

                <!-- dropdown -->

                <div class="list-group-item list-group-item-action bg-light" id="user">
                    <p>{{ Auth::user()->name }}</p>
                    <small>
                    Perfil:
                        @if (Auth::user()->role == 'administrador')
                            <span class="perfil">
                                Administrador
                            </span>
                        @else
                            <span class="perfil">
                                Recepcionista
                            </span>
                        @endif
                    </small>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>

                <a href="{{ route('admin.index') }}" class="list-group-item list-group-item-action bg-light">Dashboard Home</a>

                <a href="{{ route('admin.venda_credito') }}" class="list-group-item list-group-item-action bg-light">Venda de crédito</a>

                <a href="#" class="list-group-item list-group-item-action bg-light" data-toggle="collapse" data-target="#collapseCadastros" aria-expanded="true" aria-controls="collapseCadastros">Cadastros</a>
                <div id="collapseCadastros" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="">
                    <a href="{{ route('salas.index') }}" class="list-group-item list-group-item-action bg-light">Salas</a>
                    <a href="{{ route('usuario.index') }}" class="list-group-item list-group-item-action bg-light">Usuários</a>
                    </div>
                </div>

                {{--
                <a href="#" class="list-group-item list-group-item-action bg-light" data-toggle="collapse" data-target="#collapseAgenda" aria-expanded="true" aria-controls="collapseAgenda">Agenda</a>
                <div id="collapseAgenda" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="">
                    <a href="{{ route('admin.agenda') }}" class="list-group-item list-group-item-action bg-light">Salas</a>
                    </div>
                </div>
                <a href="#" class="list-group-item list-group-item-action bg-light" data-toggle="collapse" data-target="#collapseFinanceiro" aria-expanded="true" aria-controls="collapseFinanceiro">Financeiro</a>
                <div id="collapseFinanceiro" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="">
                    <a href="{{ route('admin.financeiro') }}" class="list-group-item list-group-item-action bg-light">Salas</a>
                    </div>
                </div>
                --}}


                <a href="#" class="list-group-item list-group-item-action bg-light" data-toggle="collapse" data-target="#collapseRelatorio" aria-expanded="true" aria-controls="collapseRelatorio">Relatórios</a>
                <div id="collapseRelatorio" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="">
                    <a href="{{ route('admin.uso_sala') }}" class="list-group-item list-group-item-action bg-light">Uso por sala</a>
                    <a href="{{ route('admin.medicos_cadastrados') }}" class="list-group-item list-group-item-action bg-light">Médicos cadastrados</a>
                    <a href="{{ route('admin.faturamento') }}" class="list-group-item list-group-item-action bg-light">Faturamento</a>
                    <a href="{{ route('admin.venda_creditos') }}" class="list-group-item list-group-item-action bg-light">Venda de créditos</a>
                    <a href="{{ route('admin.saldo_creditos') }}" class="list-group-item list-group-item-action bg-light">Saldo de créditos</a>
                    </div>
                </div>

                <div class="col text-center mt-3">
                    <a class="btn btn-default btn-block" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sair <i class="fa fa-sign-out"></i></a>
                </div>

                </div>


            </div>
            <!-- /#sidebar-wrapper -->

            <!-- Page Content -->
            <div id="page-content-wrapper">

                <div class="container-fluid">


                @yield('content')




                {{--
                <!-- admin - agendamento -->
                <h1 class="admin-title my-5">Compra de créditos</h1>

                <div class="row my-5">

                    <div class="col-12">

                    <div class="card white admin">

                        <div class="row">

                        <div class="col-12 col-md-4">
                            <div class="card-body">
                            <h5 class="card-title">Detalhes do créditos</h5>
                            <br>

                            <form action="post" class="form">
                                <div class="">
                                <div class="form-group">
                                    <label for="">Informe o nome do médico</label>
                                    <input type="text" class="form-control" placeholder="Buscar nome ou sobrenome">
                                </div>
                                <div class="form-group">
                                    <label for="">Valor</label>
                                    <input type="number" class="form-control" placeholder="1.000,00">
                                </div>


                                <br>

                                <h5 class="card-title">Endereço de cobrança</h5>
                                <br>

                                <div class="form-group">
                                    <label for="">Informe o CEP</label>
                                    <input type="text" class="form-control" placeholder="Insira seu CEP">
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-12 col-md-8">
                                    <label for="">Logradouro</label>
                                    <input type="text" class="form-control" placeholder="R. Lima Barros">
                                    </div>
                                    <div class="col-12 col-md-4">
                                    <label for="">Número</label>
                                    <input type="text" class="form-control" placeholder="Número">
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-12 col-md-7">
                                    <label for="">Complemento</label>
                                    <input type="text" class="form-control" placeholder="R. Lima Barros">
                                    </div>
                                    <div class="col-12 col-md-5">
                                    <label for="">Bairro</label>
                                    <input type="text" class="form-control" placeholder="Número">
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-12 col-md-9">
                                    <label for="">Cidade</label>
                                    <input type="text" class="form-control" placeholder="R. Lima Barros">
                                    </div>
                                    <div class="col-12 col-md-3">
                                    <label for="">Estado</label>
                                    <select name="" id="" class="form-control">
                                        <option value="sp" selected>SP</option>
                                    </select>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <button class="btn btn-default btn-block">Prosseguir para o pagamento</button>
                                </div>  -->
                                </div>
                            </form>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="card-body">
                            <h5 class="card-title">Pagamento</h5>
                            <br>
                            <form action="post" class="form">
                                <div class="">
                                <!-- toggle -->
                                <div class="mb-3">
                                    <span class="">
                                    Pagamento presencial
                                    </span>
                                    <label class="switch">
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                    </label>
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <span class="utilizar-saldo">
                                    Utilizar saldo em conta
                                    </span>
                                    <label class="switch">
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <p class="saldo-disponivel">
                                    Saldo disponível: <strong>R$ 500,00</strong>
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label for="">Informe abaixo a quantia que deseja utilizar</label>
                                    <input type="number" class="form-control" aria-describedby="valorHelp" placeholder="1.000,00">
                                    <small id="valorHelp" class="form-text text-muted help-error">Saldo insuficiente para o valor informado</small>
                                </div>

                                <div class="form-group form-row">
                                    <div class="col-12 col-md-8">
                                    <label for="">Selecione um cartão</label>
                                    <select name="" id="" class="form-control">
                                        <option value="" selected>Visa final 123</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-12">
                                    <label for="">Número do cartão</label>
                                    <input type="text" class="form-control" placeholder="5500 6522 0001 1234">
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-12 col-md-8">
                                    <label for="">Valido até</label>
                                    <input type="text" class="form-control" placeholder="11/2024">
                                    </div>
                                    <div class="col-12 col-md-4">
                                    <label for="">CVV</label>
                                    <input type="text" class="form-control" placeholder="123">
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-12">
                                    <label for="">Nome do titular</label>
                                    <input type="text" class="form-control" placeholder="Gregory House">
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-12">
                                    <label for="">Opções de pagamento</label>
                                    <select name="" id="" class="form-control">
                                        <option value="" selected>1x de R$ 1.500</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <!-- <button class="btn btn-default btn-block">Revisar agendamento</button> -->
                                </div>
                                </div>
                            </form>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <div class="card-body">
                            <h5 class="card-title">Resumo</h5>
                            <br>

                            <form action="post" class="form">
                                <div class="form-group">

                                    <p class="revisar-label">Médico</p>
                                    <h5><strong>Gregory House</strong></h5>

                                    <p class="revisar-label">Valor</p>
                                    <h5><strong>R$ 1.500</strong></h5>

                                    <p class="revisar-label">Parcelamento</p>
                                    <h5><strong>R$ 1.500 em 1x</strong></h5>

                                </div>
                                <div class="form-group my-5">
                                    <button class="btn btn-block btn-success"><i class="fa fa-check"></i> Comprar</button>
                                </div>
                                </div>
                            </form>
                            </div>
                        </div>

                        </div>

                    </div>

                    </div>


                <!-- admin - agendamento -->
                <h1 class="admin-title my-5">Compra de créditos</h1>
                <div class="row my-5">
                    <div class="col-12">
                        <div class="card white admin px-5 pt-4 pb-2 mb-5">
                            <h5 class="card-title">Resumo</h5>
                            <ul class="list-inline resumo-valores">
                            <li class="list-inline-item">Salas: <span class="valor">23</span></li>
                            <li class="list-inline-item">Salas: <span class="valor">23</span></li>
                            <li class="list-inline-item">Salas: <span class="valor">23</span></li>
                            </ul>
                        </div>
                        <div class="card white admin px-5 pt-4 pb-2">
                            <div class="row my-5">
                                <div class="col-12 table-responsive">
                                    <h5 class="title mb-5">Relatório de uso detalhado por sala</h5>
                                    <!-- datatables -->
                                    <table id="relatorio" class="display">
                                        <thead>
                                            <tr>
                                                <th>Column 1</th>
                                                <th>Column 2</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Row 1 Data 1</td>
                                                <td>Row 1 Data 2</td>
                                            </tr>
                                            <tr>
                                                <td>Row 2 Data 1</td>
                                                <td>Row 2 Data 2</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <div>
                </div>


                <!-- admin - relatórios -->

                <hr>

                <!-- admin - Cadastros -->
                <h1 class="admin-title my-5">Cadastros</h1>

                <div class="row my-5">

                    <div class="col-12">

                    <div class="card white admin px-5 pt-4 pb-2">
                        <div class="row my-5">
                        <div class="col-12">
                            <h5 class="title mb-5">Cadastros usuários</h5>

                            <form action="post" class="form">
                            <div class="">
                                <div class="form-group form-row">
                                <div class="col-12 col-md-6">
                                    <label for="nome">Nome</label>
                                    <input name="nome" type="text" class="form-control" placeholder="seu nome">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="nome">Sobrenome</label>
                                    <input name="sobrenome" type="text" class="form-control" placeholder="seu sobrenome">
                                </div>
                                </div>

                                <div class="form-group form-row">
                                <div class="col-12 col-md-4">
                                    <label for="nome">E-mail</label>
                                    <input name="nome" type="email" class="form-control" placeholder="seu e-mail">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="nome">Telefone</label>
                                    <input name="sobrenome" type="phone" class="form-control" placeholder="seu telefone">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="nome">CPF</label>
                                    <input name="sobrenome" type="text" class="form-control" placeholder="seu cpf">
                                </div>
                                </div>

                                <div class="form-group form-row">
                                <div class="col-12 col-md-6">
                                    <label for="">Senha</label>
                                    <input type="password" class="form-control" placeholder="••••••••••••">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="">Repetir Senha</label>
                                    <input type="password" class="form-control" placeholder="••••••••••••">
                                </div>
                                </div>

                                <div class="form-group form-row">
                                <div class="col-12 col-md-6">
                                    <label for="">Perfil de usuário</label>
                                    <select name="" id="" class="form-control">
                                    <option value="">A</option>
                                    </select>
                                </div>
                                </div>


                                <div class="form-group">
                                <button class="btn btn-default float-right">Cadastrar</button>
                                </div>

                            </div>
                            </form>

                        </div>
                        </div>
                    </div>



                    <div>

                </div>


                <!-- admin - relatórios -->

                <!-- admin - Cadastros -->
                <h1 class="admin-title my-5">Cadastros</h1>

                <div class="row my-5">

                    <div class="col-12">

                    <div class="card white admin px-5 pt-4 pb-2">
                        <div class="row my-5">
                        <div class="col-12">
                            <h5 class="title mb-5">Cadastros sala</h5>

                            <form action="post" class="form">
                            <div class="row">
                                <div class="col-12 col-md-6">

                                <div class="form-group form-row">
                                    <div class="col-12 col-md-8">
                                    <label for="nome">Nome da sala</label>
                                    <input name="nome" type="text" class="form-control" placeholder="nome da sala">
                                    </div>
                                    <div class="col-12 col-md-4">
                                    <label for="numero">Número</label>
                                    <input name="numero" type="text" class="form-control" placeholder="número">
                                    </div>
                                </div>

                                <div class="form-group form-row">
                                    <div class="col-12">
                                    <label for="desc">Descrição</label>
                                    <textarea name="desc" class="form-control" id="" cols="30" rows="5"></textarea>
                                    </div>
                                </div>

                                <div class="form-group form-row">
                                    <div class="col-12 col-md-6">
                                    <label for="sala">Período</label>
                                    <input type="text" id="datepicker" class="form-control" placeholder="data inicial">
                                    </div>
                                    <div class="col-12 col-md-6">
                                    <label for="sala">&nbsp;</label>
                                    <input type="text" id="datepicker" class="form-control" placeholder="data final">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">Selecion horários</label>
                                    <div class="sala-agenda">
                                    <div class="mb-3">
                                        <button class="btn btn-default-outline mx-2 my-2">
                                        07:00
                                        </button>
                                        <button class="btn btn-default-outline mx-2 my-2">
                                        07:00
                                        </button>
                                        <button class="btn btn-default-outline mx-2 my-2">
                                        07:00
                                        </button>
                                        <button class="btn btn-default-outline mx-2 my-2">
                                        07:00
                                        </button>
                                        <button class="btn btn-default-outline mx-2 my-2">
                                        07:00
                                        </button>
                                        <button class="btn btn-default-outline mx-2 my-2">
                                        07:00
                                        </button>
                                        <button class="btn btn-default-outline mx-2 my-2">
                                        07:00
                                        </button>
                                        <button class="btn btn-default-outline mx-2 my-2" disabled >
                                        07:00
                                        </button>
                                        <button class="btn btn-default-outline mx-2 my-2">
                                        07:00
                                        </button>
                                        <button class="btn btn-default-outline mx-2 my-2">
                                        07:00
                                        </button>
                                        <button class="btn btn-default-outline mx-2 my-2">
                                        07:00
                                        </button>
                                        <button class="btn btn-default-outline mx-2 my-2">
                                        07:00
                                        </button>
                                    </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="">Valor</label>
                                    <input type="number" class="form-control" placeholder="1.000,00">
                                </div>

                                </div>

                                <div class="col-12 col-md-6">
                                <label for="">Fotos</label>
                                <input type="file" class="form-control">
                                </div>

                                <div class="col-12">
                                <div class="form-group">
                                    <button class="btn btn-default float-right">Cadastrar</button>
                                </div>
                                </div>

                            </div>
                            </form>


                        </div>
                        </div>
                    </div>

                    <div>
                </div>
                <!-- admin - relatórios -->
                --}}
                </div>
            </div>


            <!-- /#page-content-wrapper -->
            </div>
            <!-- /#wrapper -->
        </div>

        <script src="{{ asset('js/vendor/jquery-3.5.1.js') }}"></script>
        <script src="{{ asset('js/vendor/jquery-ui.js') }}"></script>
        <script src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script src="https://unpkg.com/swiper/swiper-bundle.js"></script>
        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

        <!--script src="{{ asset('assets/fontawesome/js/all.min.js') }}"></script-->

        <script src="{{ asset('js/vendor/modernizr-3.11.2.min.js') }}"></script>
        <script src="{{ asset('assets/jquery_mask/dist/jquery.mask.min.js') }}"></script>
        <script src="{{ asset('assets/toastr-master/build/toastr.min.js') }}"></script>
        <script src="{{ asset('assets/chosen/chosen.jquery.min.js') }}"></script>

        <script src="{{ asset('js/plugins.js') }}"></script>
        <script src="{{ asset('js/main.js') }}"></script>

        <!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->
        <script>
        window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
        ga('create', 'UA-XXXXX-Y', 'auto'); ga('set', 'anonymizeIp', true); ga('set', 'transport', 'beacon'); ga('send', 'pageview')
        </script>
        <script src="https://www.google-analytics.com/analytics.js" async></script>
        <!-- Menu Toggle Script -->

        @yield('javascript')

        <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $('.abre-menu').addClass('aberto')
        });
        $("#menu-toggle-abre").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $('.abre-menu').removeClass('aberto')
        });

        /* 
        $( function() {
            $( "#datepickerIni" ).datepicker({ dateFormat: 'dd/mm/yy' });
            $( "#datepickerFim" ).datepicker({ dateFormat: 'dd/mm/yy' });
            $( ".datePicker" ).datepicker({ dateFormat: 'dd/mm/yy' });
        } );
        */

        $(document).ready( function () {
            $('.dtable').DataTable(
                {
                    "oLanguage": {
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
                }
            );
            $('.moeda').mask('#.##0,00', {reverse: true});
            $('.cpf').mask('000.000.000-00', {reverse: true});
            $('.chosen').chosen();

            var SPMaskBehavior = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
            },
            spOptions = {
            onKeyPress: function(val, e, field, options) {
                field.mask(SPMaskBehavior.apply({}, arguments), options);
                }
            };
            $('.telefone').mask(SPMaskBehavior, spOptions);

            $('[data-toggle="tooltip"]').tooltip();


            @if(Session::has('toastr'))
                toastr["{{ Session::get('toastr.status', 'info') }}"]("{{ Session::get('toastr.message') }}");
            @endif

        } );



        </script>

    </body>
</html>
