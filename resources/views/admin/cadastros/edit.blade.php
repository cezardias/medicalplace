@extends('layouts.admin')

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
            @if ($type == 'new')
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
                if ($('.permissao').val() == "") {
                    toastr["warning"]("Selecione a permissão");
                    $('.permissao').focus();
                    return false;
                }
            @endif
            return true;
        }


    </script>
@endsection

@section('content')
<h1 class="admin-title my-5">Cadastros</h1>
<div class="row my-5">
    <div class="col-12">
        <div class="card white admin px-5 pt-4 pb-2">
            <div class="row my-5">
                <div class="col-12">
                    

                    @if ($type == 'update')
                    <h5 class="title mb-5">Editar {{ $usuario->name }}</h5>
                        <form action="{{ route('usuario.update',[ $usuario->id ]) }}" method="post" enctype="multipart/form-data" onsubmit="return validaForm()">
                        {{ method_field('PUT') }}
                    @else 
                    <h5 class="title mb-5">Cadastrar novo usuário</h5>
                        <form action="{{ route('usuario.store') }}" method="post" enctype="multipart/form-data" onsubmit="return validaForm()">
                    @endif

                    @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group form-row">
                                    <div class="col-12 col-md-6 text-left">
                                        <label for="nome">Nome</label>
                                        <input name="nome" type="text" class="form-control nome" placeholder="Nome" value="{{ $usuario->name }}">
                                    </div>
                                    <div class="col-12 col-md-6 text-left">
                                        <label for="sobrenome">Sobrenome</label>
                                        <input name="sobrenome" type="text" class="form-control sobrenome" placeholder="Sobrenome" value="{{ $usuario->sobrenome }}">
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-12 col-md-4 text-left">
                                        <label for="email">E-mail(Será o username)</label>
                                        <input name="email" type="email" class="form-control email" placeholder="E-mail" value="{{ $usuario->email }}" @if ($type == 'update') readonly @endif>
                                    </div>
                                    <div class="col-12 col-md-4 text-left">
                                        <label for="telefone">Telefone</label>
                                        <input name="telefone" type="phone" class="form-control telefone" placeholder="Telefone" value="{{ $usuario->telefone }}">
                                    </div>
                                    <div class="col-12 col-md-4 text-left">
                                        <label for="cpf">CPF</label>
                                        <input name="cpf" type="text" class="form-control cpf" placeholder="CPF" value="{{ $usuario->cpf }}">
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-12 col-md-5 text-left">
                                        <label for="senha">Senha (digite uma nova para alterar)</label>
                                        <input name="senha" type="password" class="form-control senha" placeholder="Nova senha">
                                    </div>
                                    <div class="col-12 col-md-5 text-left">
                                        <label for="resenha">Digite a senha novamente</label>
                                        <input name="resenha" type="password" class="form-control resenha" aria-describedby="passwordHelp">
                                        <!--small id="passwordHelp" class="form-text text-muted help-error">As senhas não conferem</small-->
                                    </div>
                                    <div class="col-12 col-md-2 text-left">
                                        <label for="permissao">Permissão</label>
                                        <select name="permissao" class="form-control permissao" @if ($type == 'update') readonly @endif>
                                            <option value="">Selecione</value>
                                            <option value="medico" @if ($usuario->role == 'medico') selected @endif>Médico</value>
                                            <option value="secretaria" @if ($usuario->role == 'secretaria') selected @endif>Recepcionista</value>
                                            <option value="administrador" @if ($usuario->role == 'administrador') selected @endif>Administrador</value>
                                        </select>
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
        </div>
    <div>
</div>
@endsection