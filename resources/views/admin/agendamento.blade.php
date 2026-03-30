@extends('layouts.admin')

@section('content')
<!-- admin - agendamento -->
<h1 class="admin-title my-5">Novo agendamento</h1>

<ul class="list-inline">
    <li class="list-inline-item">
    <button class="btn btn-default-outline" data-toggle="modal" data-target="#bloquearSala"><i class="fa fa-lock"></i> Bloquear sala/horário</button>
    </li>
</ul>

<form method="POST" class="form" id="checkout">
@csrf
<div class="row my-5">
<div class="col-12">
<div class="card white admin">
    <div class="row">
        <div class="col-12">
            <div class="card-body">
                <h5 class="card-title" style="text-transform: inherit;">Preencha os detalhes de agendamento e prossiga para o pagamento</h5>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card-body">
                <div class="">
                    <div class="form-group">
                        <label for="">Selecione o médico</label>
                        <select name="medico" id="medico" class="form-control change_agendamento">
                            <option value="">Selecione</option>
                            @foreach ($medicos as $m)
                            <option value="{{ $m->id }}" @if ($m->id == $medico_selecionado) SELECTED @endif>{{ $m->name }} {{ $m->sobrenome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group form-row">
                        <div class="col-12">
                            <label for="sala">Selecione a sala</label>
                            <select name="sala" id="sala" class="form-control change_agendamento">
                                <option value="">Selecione</option>
                                @foreach ($salas as $s)
                                <option value="{{ $s->id }}" @if ($s->id == $sala_selecionada) SELECTED @endif>{{ $s->numero }}-{{ $s->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card-body">
            <div class="form-group form-row">
                        <div class="col-12 col-md-6">
                            <label for="data">Data início</label>
                            <input name="data_inicial" type="text" id="data_inicial" class="form-control dataIniFim" value="{{ $data_inicial->format('d/m/Y') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="data">Data fim</label>
                            <input name="data_final" type="text" id="data_final" class="form-control dataIniFim" value="{{ $data_final->format('d/m/Y') }}">
                        </div>
                    </div>
                <div class="form-group">
                    <label for="">Selecione abaixo um dos horários disponíveis de entrada:</label>
                    <div class="sala-agenda">
                        <div class="mb-3">
                            @foreach ($horarios as $k => $h)
                                @if (in_array($h,$horarios_sel))
                                    <input type="hidden" id="h{{ $k }}" name="horario[{{ $h }}]" value="1">
                                    <button type="button" data-id="{{ $k }}" class="btn btn-default mx-2 my-2 btn-select-horario select-horario">
                                        {{ $h }}
                                    </button>
                                @elseif ($k >= 1 && in_array($horarios[$k-1],$horarios_sel))
                                    <input type="hidden" id="h{{ $k }}" name="horario[{{ $h }}]" value="0">
                                    <button type="button" data-id="{{ $k }}" class="btn btn-default mx-2 my-2 btn-select-horario select-horario" disabled>
                                        {{ $h }}
                                    </button>
                                @else
                                    <input type="hidden" id="h{{ $k }}" name="horario[{{ $h }}]" value="0">
                                    <button type="button" data-id="{{ $k }}" class="btn btn-default-outline mx-2 my-2 btn-select-horario select-horario">
                                        {{ $h }}
                                    </button>
                                @endif
                            @endforeach
                            <p class="px-2 pt-3 pb-3" style="font-size:14px;">
                                * Para períodos maiores do que um dia, os horários selecionados deverão ser iguais.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-default float-right">Prosseguir para o pagamento</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        @if ($pagamento === true)
        {{-- BLOCO DO MEIO --}}
        <div class="col-12 col-md-6">
            <div class="card-body">
            <h5 class="card-title">Pagamento</h5>
            <br>
                <div class="">

                    <div class="form-group">
                        <input type="hidden" id="valor_total_sala" value="{{$valor}}">
                        <p class="revisar-label">Valor total</p>
                        <h5><strong>R$ {{ number_format($valor,2,',','.') }}</strong></h5>
                    </div>

                    <div class="mb-3">
                        <p class="saldo-disponivel">
                        Saldo disponível: <strong>R$ {{ number_format($credito_medico['saldo'],2,',','.') }}</strong>
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="">Informe abaixo a quantia que deseja utilizar</label>
                        <input type="text" name="valor_credito" id="valor_credito" value="0" class="form-control moeda" aria-describedby="valorHelp">
                        <small id="valorHelp" class="form-text text-muted help-error">Deixar zero para não utilizar</small>
                    </div>
                    <div class="form-group form-row">
                        <div class="col-12">
                            <label for="">Valor da cobrança</label>
                            <input type="text" name="valor_cobranca" id="valor_cobranca" value="{{ number_format($valor,2,',','.') }}" class="form-control moeda">
                            <small id="valorHelp" class="form-text text-muted help-error">Aplicar desconto se houver</small>
                        </div>
                    </div>

                    <!-- toggle -->
                    <div class="mb-3">
                        <span class="">
                        Pagamento presencial
                        </span>
                        <label class="switch">
                            <input type="checkbox" name="presencial" id="presencial" checked="true">
                            <span class="slider round"></span>
                        </label>
                    </div>

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

                    <div id="collapseCartao" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="form-group form-row">
                            <div class="col-12 col-md-8">
                                <label for="">Selecione um cartão</label>
                                <select name="cartao_precadastrado" id="cartao_precadastrado" class="form-control">
                                    <option value="" selected>Selecione um cartão</option>
                                    @forelse ($cartoes as $c)
                                        <option value="{{ $c->id }}">{{ $c->brand }} {{ str_pad($c->first_digits,8,'X') }}{{ $c->last_digits }}</option>
                                    @empty
                                        <option value="">Nenhum cartão</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-row">
                            <div class="col-12">
                                <label for="">Número do cartão</label>
                                <input type="text" class="form-control" name="numero_cartao" id="numero_cartao">
                            </div>
                        </div>
                        <div class="form-group form-row">
                            <div class="col-12 col-md-8">
                                <label for="">Valido até</label>
                                <input type="text" name="validade" id="validade" class="form-control">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="">CVV</label>
                                <input type="text" name="cvv" id="cvv" class="form-control">
                            </div>
                        </div>
                        <div class="form-group form-row">
                            <div class="col-12">
                                <label for="">Nome do titular</label>
                                <input type="text" name="nome_titular" id="nome_titular" class="form-control" placeholder="Gregory House">
                            </div>
                        </div>
                    </div>


                    {{--
                    <h5 class="card-title">Dados de cobrança</h5>
                    <br>
                    <div class="form-group">
                        <label for="">Informe o CEP</label>
                        <input type="text" id="cep" name="cep" class="form-control" placeholder="Insira seu CEP" onblur="pesquisacep(this.value);">
                    </div>
                    <div class="form-group form-row">
                        <div class="col-12 col-md-8">
                            <label for="">Logradouro</label>
                            <input type="text" id="rua" name="logradouro" class="form-control" readonly>
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="">Número</label>
                            <input type="text" id="numero" name="numero" class="form-control">
                        </div>
                    </div>
                    <div class="form-group form-row">
                        <div class="col-12 col-md-7">
                            <label for="">Complemento</label>
                            <input type="text" id="complemento" name="complemento" class="form-control">
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="">Bairro</label>
                            <input type="text" id="bairro" name="bairro" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group form-row">
                        <div class="col-12 col-md-9">
                            <label for="">Cidade</label>
                            <input type="text" id="cidade" name="cidade" class="form-control" readonly>
                        </div>
                        <div class="col-12 col-md-3">
                            <label for="">Estado</label>
                            <input type="text" id="uf" name="uf" class="form-control" readonly>
                        </div>
                    </div>
                    --}}

                </div>
            </div>
        </div>


        {{-- BLOCO DIREITA COM RESUMO --}}
        <div class="col-12 col-md-6">
            <div class="card-body">
                <h5 class="card-title">Resumo</h5>
                <br>
                <div class="">
                    <div class="form-group">
                        <p class="revisar-label">Sala selecionada</p>
                        <div class="form-row">
                            <div class="col-4">
                                <img src="{{ $sala->capa }}" alt="" class="img-fluid">
                            </div>
                            <div class="col-8">
                                <h6><strong>{{ $sala->nome }}</strong></h6>
                                <p>Núm. {{ $sala->numero }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <p class="revisar-label">Sala selecionada</p>
                        <h5><strong>{{ $data_inicial->format('d/m/Y') }} a {{ $data_final->format('d/m/Y') }}</strong></h5>
                        <h5>
                            <ul>
                            @foreach ($horarios_sel as $h)
                                <li><b>{{ $h }}</b> até <b>{{ \Carbon\Carbon::createFromFormat('H:i',$h)->addHour()->format('H:i') }}</b></li>
                            @endforeach
                            </ul>
                        </h5>
                        <p class="revisar-label">Valor</p>
                        <h5><strong>R$ R$ {{ number_format($valor,2,',','.') }} em 1x</strong></h5>
                    </div>
                    <div class="form-group my-5" id="ciencia" style="display:none;">
                        <span class="confirm-value">
                            O valor não está compatível com o custo da sala.
                        </span><br/>
                        <span class="confirm-value">
                            Estou ciente e desejo continuar.
                        </span>
                        <label class="switch">
                            <input type="checkbox" name="confirmar_valor" id="confirmar_valor">
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <div class="form-group my-5">
                        <button type="button" class="btn btn-success bt-checkout float-right"><i class="fa fa-check"></i> Fechar reserva</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- BLOCO DIREITA COM RESUMO --}}
        @else
        <div class="col-12 col-md-6">
            <div class="card-body">
                <!-- <h5 class="card-title">Preencha os detalhes de agendamento e prossiga para o pagamento</h5> -->
            </div>
        </div>
        @endif

    </div>

</div>
</div>
</div>
</div>
</form>
<!-- admin - agendamento -->

<!-- modal bloquear sala -->
<div class="modal fade" id="bloquearSala" tabindex="-1" role="dialog" aria-labelledby="bloquearSalaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

        <form action="{{ route('admin.cadastra_ocorrencia') }}" method="POST">
        @csrf

        <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Bloquear sala/horário</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
            <div class="form-group form-row">
            <div class="col-12 col-md-6">
                <label for="sala">Informe o período</label>
                <input type="text" id="datepickerIni" name="datepickerIni" class="form-control dataIniFim" placeholder="data inicial">
            </div>
            <div class="col-12 col-md-6">
                <label for="sala">&nbsp;</label>
                <input type="text" id="datepickerFim" name="datepickerFim" class="form-control dataIniFim" placeholder="data final">
            </div>
            </div>
            <div class="form-group">

            <label for="">Selecione abaixo um dos horários disponíveis de entrada:</label>
            <div class="sala-agenda">
                <div class="mb-3">
                @foreach ($horarios as $k => $h)
                    <input type="hidden" id="h_ocorr_{{ $k }}" name="horario[{{ $h }}]" value="0">
                    <button type="button" data-id="{{ $k }}" class="btn btn-default-outline mx-2 my-2 btn-select-horario select-horario-ocorrencia">
                        {{ $h }}
                    </button>
                @endforeach
                </div>
            </div>
            </div>
            <div class="form-group form-row">
                <div class="col-12 col-md-6">
                    <label for="sala">Selecione a(s) sala(s) afetadas</label>
                    <select name="salas[]" id="salas" class="form-control chosen" multiple>
                        @foreach ($salas as $s)
                        <option value="{{ $s->id }}">{{ $s->numero }}-{{ $s->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label for="motivo">Motivo do bloqueio (opcional)</label>
                    <textarea class="form-control" name="motivo" id="" cols="30" rows="5"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default-outline" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-default">Confirmar</button>
        </div>
        </form>
    </div>
    </div>
</div>
<!-- modal bloquear sala -->

<!-- modal pagamento -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <strong>Processando pagamento</strong>
                    <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal pagamento -->

@endsection

@section('style')
<style>
    .chosen-container{
        width: 100% !important;
    }
</style>
@endsection

@section('javascript')
<script>

    $(function () {
        $(".dataIniFim").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            minDate: "{{ $data_inicial->format('d/m/Y') }}"
        });
        $(".select-horario").on('click',function() {

            let horario = $(this).data('id');
            let next_horario = horario + 1;

            let sel = $('#h'+horario).val();
            let next_sel = $('#h'+next_horario).val();

            console.log(horario);
            console.log(next_horario);
            console.log(sel);
            console.log(next_sel);

            if (sel == 0 && next_sel == 0) {
                $('#h'+horario).val(1);
                $('button[data-id="'+next_horario+'"]').prop('disabled',true);
                $('button[data-id="'+next_horario+'"]').switchClass('btn-default-outline','btn-default');
            } else if (sel == 0 && next_sel == 1 || next_sel === undefined || next_sel == 3) {
                $('button[data-id="'+horario+'"]').switchClass('btn-default','btn-default-outline');
                toastr["info"]("Horário indisponível. Reserva precisa ser maior que 1 hora.");
            } else {
                $('#h'+horario).val(0);
                $('button[data-id="'+next_horario+'"]').prop('disabled',false);
                $('button[data-id="'+next_horario+'"]').switchClass('btn-default','btn-default-outline');
            }

        });

        $(".select-horario-ocorrencia").on('click',function() {
            let horario = $(this).data('id');
            let sel = $('#h_ocorr_'+horario).val();
            if (sel == 1) {
                $('#h_ocorr_'+horario).val(0);
            } else {
                $('#h_ocorr_'+horario).val(1);
            }
        });


        $('#validade').mask('00/0000');
        $('#cvv').mask('000');
        $('#numero_cartao').mask('0000 0000 0000 0000');
        $("#chosen").chosen();

        $('.collapse').collapse();
        $('#presencial').on('change',function() {
            $('#collapseCartao').collapse('toggle');
        });

        $('#cartao_precadastrado').on('change', function() {
            if ($(this).val() != "") {
                $('#numero_cartao').prop("disabled", true);
                $('#validade').prop("disabled", true);
                $('#nome_titular').prop("disabled", true);
            } else {
                $('#numero_cartao').prop("disabled", false);
                $('#validade').prop("disabled", false);
                $('#nome_titular').prop("disabled", false);
            }
        });

        $('.bt-checkout').on('click',function() {
            if ($('#medico').val() == '') {
                alert('Selecione o medico');
                $('#medico').focus();
                return false;
            }
            if ($('#sala').val() == '') {
                alert('Selecione uma sala');
                $('#sala').focus();
                return false;
            }
            if ($('#presencial').prop('checked') === false) {
                if ($('#cartao_precadastrado').val() == "" && $('#numero_cartao').val() == '') {
                    alert('Selecione um cartão pré-cadastrado ou preencha um novo');
                    $('#cartao_precadastrado').focus();
                    return false;
                }
                if ($('#cartao_precadastrado').val() == "") {
                    if ($('#numero_cartao').val() == '') {
                        alert('Preencha a validade');
                        $('#numero_cartao').focus();
                        return false;
                    }
                    if ($('#validade').val() == '') {
                        alert('Preencha a validade');
                        $('#validade').focus();
                        return false;
                    }
                    if ($('#cvv').val() == '') {
                        alert('Preencha o CVV');
                        $('#cvv').focus();
                        return false;
                    }
                    if ($('#nome_titular').val() == '') {
                        alert('Preencha o nome do titular');
                        $('#nome_titular').focus();
                        return false;
                    }
                } else {
                    if ($('#cvv').val() == '') {
                        alert('Preencha o CVV');
                        $('#cvv').focus();
                        return false;
                    }
                }
            }

            creditos = parseFloat(formataNumero($('#valor_credito').val()));
            cobranca = parseFloat(formataNumero($('#valor_cobranca').val()));
            total =  parseFloat(formataNumero($('#valor_total_sala').val()));

            if((creditos + cobranca) > total){
                toastr["error"]("Valor ultrapassou o custo da sala");
                return false;
            }

            if((creditos + cobranca) < total){
                if(!$('#confirmar_valor').prop('checked')){
                    $('#ciencia').show();
                    toastr["error"]("Valor inferior ao custo da sala, confirme a ciencia para continuar.");
                    return false;
                }
            }

            $('#exampleModalCenter').modal('toggle');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            $.ajax({
                url: "{{ route('admin.checkout_agendamento') }}",
                method: "POST",
                data: $('#checkout').serialize(),
                success: function(retorno) {
                    alert("Response received: " + JSON.stringify(retorno));
                    console.log("AJAX Success:", retorno);
                    if (retorno.status == false) {
                        toastr["warning"](retorno.message);
                    } else {
                        toastr["success"](retorno.message);
                        window.location.replace("{{ route('admin.agendamento') }}");
                    }
                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr.status, xhr.responseText);
                    toastr["error"]("Erro no servidor ao processar o agendamento.");
                },
                complete: function() {
                    console.log("AJAX Complete");
                    $('#exampleModalCenter').modal('hide');
                    // Force backdrop removal just in case
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                }
            });


        });

        @if(!empty($msg_ocorrencias))
            @foreach ($msg_ocorrencias as $msg)
                toastr["warning"]("{!! $msg !!}");
            @endforeach
        @endif

    });

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
                alert("Formato de CEP inválido.");
            }
        } else {
            limpa_formulário_cep();
        }
    };

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
