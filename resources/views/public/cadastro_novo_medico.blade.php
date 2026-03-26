@extends('layouts.public')

@section('javascript')
    <script>
        
        $('.resenha').on('change', function() { verifyRePass(); });
        $('.senha').on('change', function() { verifyRePass(); });
        function verifyRePass() {
            if ($('.senha').val() != "" && $('.resenha').val() != "") {
                if ($('.senha').val() != $('.resenha').val()) {
                    toastr["error"]("Senha não confere");
                    $('.resenha').val("")
                    $(this).focus();
                }
            }
        }

        function validaForm() {
            if ($('.nome').val() == "") {
                toastr["warning"]("Preencha o nome");
                $('.nome').focus();
                return false;
            }
            if ($('.sobrenome').val() == "") {
                toastr["warning"]("Preencha o sobrenome");
                $('.sobrenome').focus();
                return false;
            }
            if ($('.telefone').val() == "") {
                toastr["warning"]("Preencha o telefone");
                $('.telefone').focus();
                return false;
            }
            if ($('.cpf').val() == "") {
                toastr["warning"]("Preencha o cpf");
                $('.cpf').focus();
                return false;
            }
            if ($('.email').val() == "") {
                toastr["warning"]("Preencha o E-mail");
                $('.email').focus();
                return false;
            }
            if ($('.senha').val() == "") {
                toastr["warning"]("Preencha a senha");
                $('.senha').focus();
                return false;
            }
            if ($('.resenha').val() == "") {
                toastr["warning"]("Confirme a senha");
                $('.resenha').focus();
                return false;
            }
            return true;
        }
    </script>
@endsection

@section('content')
<div class="container my-5" id="contentWrapper">
    <div class="row text-center">   
        <div class="col-12">
            <h1 class="title">{{ __('Cadastre-se') }}</h1>
        </div>
    </div>  
    <div class="row">
        <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cadastre-se</li>
            </ol>
        </nav>
        </div>
    </div>
    <div class="row text-left">
      


        <form method="POST" action="{{ route('cadastro_novo_medico') }}" class="col-12 col-md-10 offset-md-1 my-5 form" onsubmit="return validaForm()">
        @csrf
            <div class="alert alert-primary" role="alert">Insira seus dados</div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group form-row">
                        <div class="col-12 col-md-6 text-left">
                            <label for="nome">Nome</label>
                            <input name="nome" type="text" class="form-control nome" placeholder="Nome">
                        </div>
                        <div class="col-12 col-md-6 text-left">
                            <label for="sobrenome">Sobrenome</label>
                            <input name="sobrenome" type="text" class="form-control sobrenome" placeholder="Sobrenome">
                        </div>
                    </div>
                    <div class="form-group form-row">
                        <div class="col-12 col-md-4 text-left">
                            <label for="email">E-mail(Será o username)</label>
                            <input name="email" type="email" class="form-control email" placeholder="E-mail">
                        </div>
                        <div class="col-12 col-md-4 text-left">
                            <label for="telefone">Telefone</label>
                            <input name="telefone" type="phone" class="form-control telefone" placeholder="Telefone">
                        </div>
                        <div class="col-12 col-md-4 text-left">
                            <label for="cpf">CPF</label>
                            <input name="cpf" type="text" class="form-control cpf" placeholder="CPF">
                        </div>
                    </div>
                    <div class="form-group form-row">
                        <div class="col-12 col-md-6 text-left">
                            <label for="senha">Senha (digite uma nova para alterar)</label>
                            <input name="senha" type="password" class="form-control senha" placeholder="Nova senha">
                        </div>
                        <div class="col-12 col-md-6 text-left">
                            <label for="resenha">Digite a senha novamente</label>
                            <input name="resenha" type="password" class="form-control resenha" aria-describedby="passwordHelp">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-default float-right">Cadastrar</button>
                    </div>
                </div>
            </div>   



        </form>


    </div>
</div>
@endsection
