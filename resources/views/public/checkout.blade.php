@extends('layouts.public')

@section('javascript')
<script>
    function limpa_formulário_cep() {
        $('#rua').val("");
        $('#bairro').val("");
        $('#cidade').val("");
        $('#uf').val("");
    }
    function meu_callback(conteudo) {
        if (!("erro" in conteudo)) {
            $('#rua').val(conteudo.logradouro);
            $('#bairro').val(conteudo.bairro);
            $('#cidade').val(conteudo.localidade);
            $('#uf').val(conteudo.uf);
        } else {
            limpa_formulário_cep();
            alert("CEP não encontrado.");
        }
    }
    function pesquisacep(valor) {
        var cep = valor.replace(/\D/g, '');
        if (cep != "") {
            var validacep = /^[0-9]{8}$/;
            if(validacep.test(cep)) {
                $('#rua').val("...");
                $('#bairro').val("...");
                $('#cidade').val("...");
                $('#uf').val("...");
                var script = document.createElement('script');
                script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';
                document.body.appendChild(script);
            } else {
                limpa_formulário_cep();
                toastr["error"]("Formato de CEP inválido.");
            }
        } else {
            limpa_formulário_cep();
        }
    };

    $('.remove_horario').on('click',function() {
        let el = $(this).data('id');
        $('#'+el).remove();
    });

    function checkValidDate(valid_date) {
        let month = "{{ \Carbon\Carbon::now()->format('m') }}";
        let year = "{{ \Carbon\Carbon::now()->format('y') }}";
        let split_date = valid_date.split('/');

        if (split_date[1] < year) {
            return false;
        } else if (split_date[1] == year && split_date[0] < month) {
            return false;
        } else if (split_date[0] > 12) {
            return false;
        }
        return true;
    }

    function checkLength(str, size) {
        console.log(str.replace(/ /g,"").length);
        if (str.replace(/ /g,"").length < size) {
            return false;
        }
        return true;
    }

    $('.bt-checkout').on('click',function() {

        enableCardDataForPre();

        if (!calcCharge()) {
            if ($('#card_id').length > 0 && $('#card_id').val() != "") {
                if ($('#cvv').val() == '') {
                    toastr["error"]("Preencha o CVV");
                    $('#cvv').focus();
                    return false;
                }
            } else {
                if ($('#numero_cartao').val() == '') {
                    toastr["error"]("Preencha o número do cartão");
                    $('#numero_cartao').focus();
                    return false;
                }
                if (!checkLength($('#numero_cartao').val(),16)) {
                    toastr["error"]("Número de cartão inválido");
                    $('#numero_cartao').focus();
                    return false;
                }
                if ($('#validade').val() == '') {
                    toastr["error"]("Preencha a validade");
                    $('#validade').focus();
                    return false;
                }
                if (checkValidDate($('#validade').val()) === false) {
                    toastr["error"]("Validade inválida");
                    $('#validade').focus();
                    return false;
                }
                if ($('#cvv').val() == '') {
                    toastr["error"]("Preencha o CVV");
                    $('#cvv').focus();
                    return false;
                }
                if (!checkLength($('#cvv').val(),3)) {
                    toastr["error"]("CVV inválido");
                    $('#cvv').focus();
                    return false;
                }
                if ($('#nome_titular').val() == '') {
                    toastr["error"]("Preencha o nome do titular");
                    $('#nome_titular').focus();
                    return false;
                }
            }
        }
        $('#exampleModalCenter').modal('toggle');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        $.ajax({
            url: "{{ route('checkout_pagamento') }}",
            method: "POST",
            data: $('#checkout').serialize(),
            complete: function(response) {
                let retorno = jQuery.parseJSON(response.responseText);
                if (retorno.status == false) {
                    toastr["warning"](retorno.message);
                    $('#exampleModalCenter').modal('toggle');
                } else {
                    toastr["success"](retorno.message);
                    window.location.replace("{{ route('minha_conta') }}");
                }

                $('#exampleModalCenter').modal('toggle');
            }
        });
    });

    $('.entrada_creditos').on('change', function() { calcCharge(); });
    $('#card_id').on('change', function() { enableCardDataForPre(); });
    $(document).ready( function () {
        $('#validade').mask('00/00');
        $('#cvv').mask('000');
        $('#numero_cartao').mask('0000 0000 0000 0000');
        enableCardDataForPre();
        calcCharge();
    });

    function calcCharge() {
        let total = parseFloat($('.valor_total').val());

        let creditos_disponiveis = 0;
        let creditos = 0;

        if ( $('.entrada_creditos').length ) {
            creditos_disponiveis = parseFloat($('.creditos_disponiveis').val());
            creditos = parseFloat(formataNumero($('.entrada_creditos').val()));
        }

        if (creditos > creditos_disponiveis || creditos > total) {

            if(creditos > creditos_disponiveis){
                toastr["error"]("Valor ultrapassou créditos disponíveis");
            }

            if(creditos > total){
                toastr["error"]("Valor ultrapassou o custo da sala");
            }

            if (total <= creditos_disponiveis) {
                $('.entrada_creditos').val(total.toLocaleString('pt-br', {minimumFractionDigits: 2}));
            } else {
                $('.entrada_creditos').val(creditos_disponiveis.toLocaleString('pt-br', {minimumFractionDigits: 2}));
            }
            calcCharge();
            return false;
        }

        let resto = total - creditos;


        $('.valor_creditos').html($('.entrada_creditos').val());
        $('.valor_cartao').html((resto.toLocaleString('pt-br', {minimumFractionDigits: 2})));

        if (resto == 0) {
            enableCardSelection(false);
            enableAllCardData(false);
            return true;
        } else {
            enableCardSelection();
            enableAllCardData();
            return false;
        }
    }

    function enableCardSelection(status = true) {
        if (!status) {
            $('#card_id').prop('disabled','disabled');
        } else {
            $('#card_id').prop('disabled',false);
        }
    }
    function enableAllCardData(status = true) {
        if (!status) {
            $('#numero_cartao').prop('disabled','disabled');
            $('#validade').prop('disabled','disabled');
            $('#cvv').prop('disabled','disabled');
            $('#nome_titular').prop('disabled','disabled');
        } else {
            $('#numero_cartao').prop('disabled',false);
            $('#validade').prop('disabled',false);
            $('#cvv').prop('disabled',false);
            $('#nome_titular').prop('disabled',false);
        }
    }

    function enableCardDataForPre(){
        if ($("#card_id").length > 0 && $("#card_id").val() != "") {
            enableAllCardData(false);
            $('#cvv').prop('disabled',false);
        } else {
            enableAllCardData();
        }
    }


    function formataNumero(n) {
        if (n === "") {
            n =  0;
        } else {
            n = n.split('.').join("");
            n = n.replace(",",".");
        }
	return n;
}

</script>
@endsection

@section('content')
<div class="container my-5" id="contentWrapper">
    <div class="row text-center">
        <div class="col-12">
        <h1 class="title">Finalizar reserva</h1>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="row">
        <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Salas</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="{{ route('ver_sala',[ $sala->slug ]) }}">{{ $sala->nome }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Finalizar Reserva</li>
            </ol>
        </nav>
        </div>
    </div>
    <!--Breadcrumb -->
    {{--
    <form method="post" id="checkout" action="{{ route('checkout_pagamento') }}">
    --}}
    <form method="post" id="checkout" onsubmit="return false;">
    @csrf
        <div class="row text-left">
            <div class="col-12 col-md-6 my-5 form">
                <div class="alert alert-primary" role="alert">
                    Dados do pagamento
                </div>
                <div class="container">
                    <!-- toggle -->
                    {{--
                    <div class="mb-3">
                        <span class="utilizar-saldo">
                            Utilizar saldo em conta
                        </span>
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    --}}

                    @if ($creditos['saldo'] > 0)
                    <input type='hidden' class='creditos_disponiveis' value="{{ $creditos['saldo'] }}">
                    <div class="mb-3">
                        <p class="saldo-disponivel">
                            Saldo disponível: <strong>R$ {{ number_format($creditos['saldo'],2,',','.') }}</strong>
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="">Informe abaixo a quantia que deseja utilizar</label>
                        <input type="text" class="form-control moeda entrada_creditos" name="credito_selecionado" aria-describedby="valorHelp" placeholder="00,00">
                    </div>
                    @endif

                    @if (count($cartoes_cadastrados) > 0)
                    <div class="form-group form-row">
                        <div class="col-12 col-md-12">
                            <label for="">Selecione um cartão cadastrado</label>
                            <select name="card_id" id="card_id" class="form-control">
                                    <option value="" selected>Novo cartão</option>
                                @forelse ($cartoes_cadastrados as $cartao)
                                    <option value="{{ $cartao->id }}">{{ $cartao->brand }} final {{ $cartao->last_digits }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="form-group form-row">
                        <div class="col-12">
                            <label for="">Número do cartão</label>
                            <input type="text" id="numero_cartao" name="numero_cartao" class="form-control" placeholder="0000 0000 0000 0000">
                        </div>
                    </div>
                    <div class="form-group form-row">
                        <div class="col-12 col-md-8">
                            <label for="">Valido até</label>
                            <input type="text" id="validade" name="validade" class="form-control validade" placeholder="MM/AA">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="">CVV</label>
                            <input type="text" id="cvv" name="cvv" class="form-control" placeholder="XXX">
                        </div>
                    </div>
                    <div class="form-group form-row">
                        <div class="col-12">
                            <label for="">Nome do titular</label>
                            <input type="text" id="nome_titular" name="nome_titular" class="form-control" placeholder="Titular">
                        </div>
                    </div>
                    <div class="mb-3">
                        <span class="utilizar-saldo">
                            Gravar cartão
                        </span>
                        <label class="switch">
                            <input type="checkbox" name="gravar_cartao">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    {{--
                    <div class="form-group form-row">
                        <div class="col-12">
                            <label for="">Opções de pagamento</label>
                            <select name="" id="" class="form-control">
                                <option value="" selected>1x de R$ {{ number_format($valor_total,2,'.',',')}}</option>
                            </select>
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="form-group">
                        <button class="btn btn-default btn-block" type="button">Revisar agendamento</button>
                    </div>
                    --}}
                </div>
            </div>
            <div class="col-12 col-md-6 my-5 form">
                <div class="alert alert-primary" role="alert">
                    Revisar pedido de agendamento
                </div>
                <div class="container">
                    <div class="form-group">
                        <p class="revisar-label">Sala selecionada</p>
                        <input type="hidden" name="sala" value="{{ $sala->id }}">
                        <div class="form-row">
                            <div class="col-4">
                            <img src="{{ asset($sala->capa) }}" alt="" class="img-fluid">
                            </div>
                            <div class="col-8">
                                <h6><strong>{{ $sala->nome }}</strong></h6>
                                <p>Núm. {{ $sala->numero }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <p class="revisar-label">Horário(s) selecionados para o dia {{ $data_agendamento }}</p>
                        <input type="hidden" name="data_agendamento" value="{{ $data_agendamento }}">
                        @forelse ($horario_selecionado as $hora => $val)
                        <div id="horario_{{ str_replace(':','',$hora) }}">
                            <input type="hidden" name="horario[{{ $hora }}]" value="1">
                            <h5><button class='btn btn-sm btn-outline-danger remove_horario' data-id="horario_{{ str_replace(':','',$hora) }}"><i class="fa fa-times" aria-hidden="true"></i></button>&nbsp;<strong>{{ $hora }} até {{ \Carbon\Carbon::createFromFormat('H:i',$hora)->addHour()->format('H:i') }} h</strong></h5>
                        </div>
                        @empty
                        @endforelse
                        <p class="revisar-label">Valor total a pagar</p>
                        <input type="hidden" class="valor_total" value="{{ $valor_total }}">
                        <h5><strong>R$ {{ number_format($valor_total,2,',','.')}} em 1x</strong></h5>

                        <p class="revisar-label">Créditos utilizados</p>
                        <h5><strong>R$ <span class="valor_creditos">{{ number_format(0,2,',','.')}}</span></strong></h5>

                        <p class="revisar-label">A cobrar no crédito</p>
                        <h5><strong>R$ <span class="valor_cartao">{{ number_format($valor_total,2,',','.')}}</span></strong></h5>


                    </div>
                    <div class="form-group my-5">
                        <button class="btn btn-block btn-success bt-checkout" type="button"><i class="fa fa-check"></i> Finalizar reserva</button>
                        {{--
                        <button class="btn btn-block btn-success" type="submit"><i class="fa fa-check"></i> Finalizar reserva</button>
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="alert alert-light" role="alert">
        <h6>
            * O cancelamento do agendamento poderá ser realizado até às 22 horas do dia anterior.
        </h6>
    </div>
</div>

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            {{--
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            --}}
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <strong>Processando pagamento</strong>
                    <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                </div>
            </div>
            {{--
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
            --}}
        </div>
    </div>
</div>

@endsection
