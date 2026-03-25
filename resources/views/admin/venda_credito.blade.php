@extends('layouts.admin')

@section('content')
<!-- admin - agendamento -->
<h1 class="admin-title my-5">Venda de crédito</h1>

{{--
<ul class="list-inline">
    <li class="list-inline-item">
    <button class="btn btn-default-outline" data-toggle="modal" data-target="#bloquearSala"><i class="fa fa-lock"></i> Bloquear sala/horário</button>
    </li>
</ul>
--}}

<div class="row my-5">
    <div class="col-12">
    <div class="card white admin">
        <form id="checkout" method='post'>
        @csrf
        <input type="hidden" name="produto" value="1009">
        <div class="row">
            <div class="col-12 col-md-6 offset-md-3">
                <div class="card-body">
                <h5 class="card-title">Médico</h5>
                    <br>
                    <div class="">
                        <div class="form-group">
                            <label for="">Selecione o médico</label>
                            <select name="medico" id="medico" class="form-control change_agendamento">
                                <option value="">Selecione</option>
                                @foreach ($medicos as $m)
                                <option value="{{ $m->id }}" @if ($m->id == $medico) selected @endif>{{ $m->name }} {{ $m->sobrenome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{--
                    <br>
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
                <div class="card-body">
                    <h5 class="card-title">Pagamento</h5>
                    <br>
                    <div class="">
                        <div class="form-group form-row">
                            <div class="col-12">
                                <label for="">Valor do crédito</label>
                                <input type="text" id="valor_credito" name="valor_credito" class="form-control moeda">
                            </div>
                        </div>
                        <div class="form-group form-row">
                            <div class="col-12">
                                <label for="">Valor da cobrança</label>
                                <input type="text" id="valor_cobranca" name="valor_cobranca" class="form-control moeda">
                            </div>
                        </div>
                        <!-- toggle -->
                        <div class="mb-3">
                            <span class="">Pagamento presencial</span>
                            <label class="switch">
                                <input type="checkbox" name="presencial" id="presencial" checked="true">
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <div id="collapseCartao" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                            <hr>
                            <div class="form-group form-row">
                                <div class="col-12 col-md-8">
                                    <label for="cartao_precadastrado">Cartões cadastrados</label>
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
                                    <input type="text" class="form-control" id="numero_cartao" name="numero_cartao" placeholder="5500 6522 0001 1234">
                                </div>
                            </div>
                            <div class="form-group form-row">
                                <div class="col-12 col-md-8">
                                    <label for="">Valido até</label>
                                    <input type="text" class="form-control" id="validade" name="validade" placeholder="11/2024">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="">CVV</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123">
                                </div>
                            </div>
                            <div class="form-group form-row">
                                <div class="col-12">
                                    <label for="">Nome do titular</label>
                                    <input type="text" class="form-control" id="nome_titular" name="nome_titular">
                                </div>
                            </div>
                            <!-- toggle -->
                            {{--
                            <div class="mb-3">
                                <span class="">Cadastrar cartão</span>
                                <label class="switch">
                                    <input type="checkbox" name="gravar_cartao" id="gravar_cartao">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            --}}
                        </div>

                        <div class="form-group my-5">
                            <button type="button" class="btn btn-block btn-success bt-checkout"><i class="fa fa-check"></i> Confirmar compra</button>
                            <!--button type="submit" class="btn btn-block btn-success"><i class="fa fa-check"></i> Confirmar compra</button-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    </div>
</div>

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

<!-- admin - agendamento -->


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

    $(document).ready( function () {

        $('#validade').mask('00/0000');
        $('#cvv').mask('000');
        $('#numero_cartao').mask('0000 0000 0000 0000');
        $("#chosen").chosen();

        $('.collapse').collapse();
        $('#presencial').on('change',function() {
            $('#collapseCartao').collapse('toggle');
        });

        $('#medico').on('change',function() {
            $('#checkout').submit();
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
            if ($('#cep').val() == '') {
                alert('Preencha o CEP');
                $('#cep').focus();
                return false;
            }
            if ($('#numero').val() == '') {
                alert('Preencha o número do endereço');
                $('#numero').focus();
                return false;
            }
            if ($('#valor_credito').val() == '') {
                alert('Preencha o valor para crédito');
                $('#valor_credito').focus();
                return false;
            }
            if ($('#valor_cobranca').val() == '') {
                alert('Preencha o valor para cobrança');
                $('#valor_cobranca').focus();
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

            $('#exampleModalCenter').modal('toggle');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            $.ajax({
                url: "{{ route('admin.checkout_venda_credito') }}",
                method: "POST",
                data: $('#checkout').serialize(),
                complete: function(response) {
                    let retorno = jQuery.parseJSON(response.responseText);
                    if (retorno.status == false) {
                        toastr["warning"](retorno.message);
                    } else {
                        toastr["success"](retorno.message);
                    }
                    $('#exampleModalCenter').modal('hide');
                }
            });
        });





    });


    /*
    $(function () {
        $('#presencial').on('change',function() {
            $('#collapseCartao').collapse('toggle');
        });
        $("#chosen").chosen();
    });
    */
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



</script>
@endsection
