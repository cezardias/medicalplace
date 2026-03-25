@extends('layouts.public')

@section('javascript')
    <script>
        var SPMaskBehavior = function(val) {
                return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
            },
            spOptions = {
                onKeyPress: function(val, e, field, options) {
                    field.mask(SPMaskBehavior.apply({}, arguments), options);
                }
            };
        $('.telefone').mask(SPMaskBehavior, spOptions);
        $('.cpf').mask('000.000.000-00', {
            reverse: true
        });
    </script>
@endsection

@section('content')
    <div class="container my-5" id="contentWrapper">
        <div class="row text-center">
            <div class="col-12">
                <h1 class="title">Minha conta</h1>
            </div>
        </div>

        <!-- Médico logado navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light mt-5">
            <!-- <a class="navbar-brand" href="#">Navbar</a> -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMedico"
                aria-controls="navbarMedico" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMedico">
                <ul class="navbar-nav mx-auto pt-3 text-center">
                    @if (!empty($proximas[0]))
                        <li class="nav-item active">
                            <p class="nav-link">Próxima consulta: <span
                                    class="destaque">{{ \Carbon\Carbon::createfromformat('Y-m-d', $proximas[0]->data)->format('d/m/Y') }}
                                    {{ \Carbon\Carbon::createfromformat('H:i:s', $proximas[0]->hora)->format('H:i') }}h</span>
                                &nbsp;Sala: <span class="destaque">{{ $proximas[0]->nome }}</span></p>
                        </li>
                        <li class="nav-item pt-1">
                            @if ($proximas[0]->pode_cancelar)
                                {{-- <a class="btn btn-danger" href="{{ route('cancelar_reserva') }}" onclick="event.preventDefault(); document.getElementById('cancelar_dest_{{ $proximas[0]->id }}').submit();">Cancelar consulta</a> --}}
                                <button class="btn btn-danger" href="{{ route('cancelar_reserva') }}"
                                    id="cancelar_reserva_{{ $proximas[0]->id }}"
                                    onclick="processarCancelamento(event, '{{ $proximas[0]->id }}')">Cancelar
                                    consulta</button>
                                <form action="{{ route('cancelar_reserva') }}" id="cancelar_dest_{{ $proximas[0]->id }}"
                                    method="post">
                                    @csrf
                                    <input type="hidden" name="reserva" value="{{ $proximas[0]->id }}">
                                </form>
                            @else
                                <button class="btn btn-secondary" data-toggle="tooltip" data-placement="top"
                                    title="O cancelamento só pode ser feito até às 22 horas do dia anterior à consulta."
                                    href="#" readonly>Cancelar consulta</button>
                            @endif
                        </li>
                        <!-- <li class="nav-item">
                              <a class="nav-link" onclick="$('#historico-tab').trigger('click')">ver próx. agendamentos</a>
                            </li> -->
                    @else
                        <li class="nav-item active">
                            <p class="nav-link">Próx. consulta: <span class="destaque">Não há reserva.</span></p>
                        </li>
                    @endif
                </ul>

                <!--ul class="navbar-nav ml-auto">
                            <li class="nav-item active">
                              <p class="nav-link">Saldo: <span class="destaque">R$ 500,00</span></p>
                            </li>
                            <li class="nav-item active">
                              <p class="nav-link">Cartão ativo: <span class="destaque">Mastercard final 1234</span></p>
                            </li>
                          </ul-->
            </div>
        </nav>
        <!-- Médico logado navbar -->


        <ul class="nav nav-tabs justify-content-center mt-5" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="dados-tab" data-toggle="tab" href="#dados" role="tab"
                    aria-controls="home" aria-selected="true">Meus dados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="carteira-tab" data-toggle="tab" href="#carteira" role="tab"
                    aria-controls="profile" aria-selected="false">Carteira</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="historico-tab" data-toggle="tab" href="#historico" role="tab"
                    aria-controls="contact" aria-selected="false">Histórico</a>
            </li>
        </ul>

        <div class="tab-content mt-5" id="myTabContent">
            <div class="tab-pane fade show active" id="dados" role="tabpanel" aria-labelledby="dados-tab">

                <form class="col-12 my-5 form" method="post">
                    @csrf
                    <h5 class="title mb-5">Dados pessoais</h5>

                    <div class="form-group form-row">
                        <div class="col-12 col-md-6 text-left">
                            <label for="name">Nome</label>
                            <input name="name" type="text" class="form-control" placeholder="Nome"
                                value="{{ $user->name }}">
                        </div>
                        <div class="col-12 col-md-6 text-left">
                            <label for="sobrenome">Sobrenome</label>
                            <input name="sobrenome" type="text" class="form-control" placeholder="Sobrenome"
                                value="{{ $user->sobrenome }}">
                        </div>
                    </div>

                    <div class="form-group form-row">
                        <div class="col-12 col-md-4 text-left">
                            <label for="email">E-mail</label>
                            <input name="email" type="email" class="form-control" placeholder="E-mail" readonly
                                value="{{ $user->email }}">
                        </div>
                        <div class="col-12 col-md-4 text-left">
                            <label for="telefone">Telefone</label>
                            <input name="telefone" type="phone" class="form-control telefone" placeholder="Telefone"
                                value="{{ $user->telefone }}">
                        </div>
                        <div class="col-12 col-md-4 text-left">
                            <label for="cpf">CPF</label>
                            <input name="cpf" type="text" class="form-control cpf" placeholder="CPF"
                                value="{{ $user->cpf }}">
                        </div>
                    </div>

                    <div class="form-group form-row">
                        <div class="col-12 col-md-6 text-left">
                            <label for="senha">Senha (digite uma nova para alterar)</label>
                            <input name="senha" type="password" class="form-control" placeholder="Senha">
                        </div>
                        <div class="col-12 col-md-6 text-left">
                            <label for="resenha">Digite a senha novamente</label>
                            <input name="resenha" type="password" class="form-control" placeholder="Confirme a senha"
                                aria-describedby="passwordHelp">
                            {{-- <small id="passwordHelp" class="form-text text-muted help-error">As senhas não conferem</small> --}}
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default float-right">Atualizar cadastro</button>
                    </div>
                </form>


                <div class="row">
                    <div class="col-12 mt-5">
                        <h5 class="title mb-5">Cartões de crédito salvos</h5>
                    </div>
                </div>

                <table class="table table-hover" style="height: 100px;" id="meusCartoes">
                    <tbody>
                        @forelse ($cartoes as $c)
                            <tr>
                                <td class="align-middle">
                                    <h5 class="head">Bandeira</h5>
                                    <p class="content">
                                        {{ $c->brand }}
                                    </p>
                                    <!--img src="https://via.placeholder.com/150x75" alt="Logotipo cartão"-->
                                </td>
                                <td class="align-middle">
                                    <h5 class="head">Número</h5>
                                    <p class="content">
                                        {{ str_pad($c->first_digits, 8, '*') }}{{ str_pad($c->last_digits, 8, '*', STR_PAD_LEFT) }}
                                    </p>
                                </td>
                                <td class="align-middle">
                                    <h5 class="head">Validade</h5>
                                    <p class="content">{{ $c->exp_month }}/{{ $c->exp_year }}</p>
                                </td>
                                <td class="align-middle">
                                    <h5 class="head">Nome no cartão</h5>
                                    <p class="content">{{ $c->holder }}</p>
                                </td>
                                <td class="align-middle">
                                    @if ($c->principal == 1)
                                        <span>
                                            Cartão<br>
                                            principal
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <button class="btn btn-default">
                                        Principal
                                    </button>
                                    <button class="btn btn-danger">
                                        Remover
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="align-middle">
                                    Nenhum cartão cadastrado. Efetue uma reserva e grave seu cartão.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{--
        <div class="row">
          <div class="col-12 col-md-4 offset-md-8 float-right">
            <button class="btn btn-default btn-block" data-toggle="modal" data-target="#adicionarCartao">Adicionar cartão</button>
          </div>
        </div>
        --}}

            </div>


            <div class="tab-pane fade" id="carteira" role="tabpanel" aria-labelledby="carteira-tab">

                <div class="container">

                    <div class="row mb-5">
                        <div class="col-12">
                            <h5 class="title-saldo">Saldo em conta <span>R$
                                    {{ number_format($creditos['saldo'], 2, ',', '.') }}</span></h5>
                        </div>
                    </div>

                    {{--
          <div class="row mb-5">
            <div class="col-12 table-responsive">
              <table class="table table-hover" id="tableCarteira">
                <tbody>
                  <tr>
                    <td>03 Jun</td>
                    <td>Saldo anterior</td>
                    <td class="saldo">R$ 1.200,00</td>
                  </tr>
                  <tr>
                    <td>04 Jun</td>
                    <td><strong>Saldo disponível p/ utilização</strong></td>
                    <td class="saldo">R$ 500,00</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          --}}

                    <div class="row mb-5">
                        <div class="col-12 table-responsive">
                            <h5 class="title mb-5">Extrato</h5>
                            <table class="table table-hover" id="tableCarteira">
                                <thead>
                                    <tr>
                                        <td class="align-middle">Data</td>
                                        <td class="align-middle">Lançamento</td>
                                        <td class="align-middle">Valor</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($creditos['lancamentos'] as $l)
                                        <tr>
                                            <td class="align-middle">
                                                {{ \Carbon\Carbon::createfromformat('Y-m-d H:i:s', $l->created_at)->format('d/m/Y H:i:s') }}
                                            </td>
                                            @if ($l->tipo == 'debito')
                                                <td class="align-middle">Débito</td>
                                                <td class="align-middle saldo-debitado">- R$
                                                    {{ number_format($l->valor, 2, ',', '.') }}</td>
                                            @else
                                                <td class="align-middle">Crédito</td>
                                                <td class="align-middle">R$ {{ number_format($l->valor, 2, ',', '.') }}
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">Nenhuma movimentação</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <div class="tab-pane fade" id="historico" role="tabpanel" aria-labelledby="historico-tab">


                <div class="row mb-5">
                    <div class="col-12 table-responsive">
                        <h5 class="title mb-5">Próximos agendamentos</h5>
                        <table class="table table-hover" id="tableHistorico">
                            <tbody>
                                @forelse ($proximas as $p)
                                    <tr>
                                        <td class="align-middle">
                                            {{ \Carbon\Carbon::createfromformat('Y-m-d', $p->data)->format('d/m/Y') }}</td>
                                        <td class="align-middle">{{ $p->hora }}</td>
                                        <td class="align-middle">{{ $p->nome }}</td>
                                        <td class="align-middle">
                                            @if ($p->pode_cancelar)
                                                {{-- <a class="btn btn-danger" href="{{ route('cancelar_reserva') }}"
                                                    onclick="event.preventDefault(); document.getElementById('cancelar_{{ $p->id }}').submit();">Cancelar
                                                    consulta</a> --}}
                                                <button class="btn btn-danger cancelar_reserva_{{ $p->id }}"
                                                    onclick="processarCancelamento2(event, '{{ $p->id }}')">Cancelar
                                                    consulta</button>
                                                <form action="{{ route('cancelar_reserva') }}"
                                                    id="cancelar_{{ $p->id }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="reserva" value="{{ $p->id }}">
                                                </form>
                                            @else
                                                <button class="btn btn-secondary" data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="O cancelamento só pode ser feito até às 22 horas do dia anterior à consulta."
                                                    href="#" readonly>Cancelar consulta</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="align-middle">Nenhum agendamento futuro</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-12 table-responsive">
                        <h5 class="title mb-5">Histórico de agendamentos</h5>
                        <table class="table table-hover" id="tableHistorico">
                            <tbody>
                                @forelse ($anteriores as $a)
                                    <tr>
                                        <td class="align-middle">
                                            {{ \Carbon\Carbon::createfromformat('Y-m-d', $a->data)->format('d/m/Y') }}</td>
                                        <td class="align-middle">{{ $a->hora }}</td>
                                        <td class="align-middle">{{ $a->nome }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="align-middle">Nenhum histórico anterior</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function processarCancelamento(event, id) {
            if (confirm('Tem certeza que deseja cancelar esta consulta?')) {
                $("#cancelar_reserva_" + id).prop("disabled", true);
                document.getElementById('cancelar_dest_' + id).submit();
            }
        }


        function processarCancelamento2(event, id) {
            if (confirm('Tem certeza que deseja cancelar esta consulta?')) {
                $(".cancelar_reserva_" + id).prop("disabled", true);
                document.getElementById('cancelar_' + id).submit();
            }
        }
    </script>
@endsection
