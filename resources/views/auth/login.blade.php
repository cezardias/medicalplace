@extends('layouts.public')

@section('content')
<div class="container my-5" id="contentWrapper">
    <div class="row text-center">   
        <div class="col-12">
            <h1 class="title">{{ __('Login') }}</h1>
        </div>
    </div>  
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Login</li>
            </ol>
        </nav>
        </div>
    </div>
    <!--Breadcrumb -->
    <div class="row text-left">

        @if(session()->has('sala'))
        <div class="col-12 col-md-6 my-5">
            <div class="alert alert-primary" role="alert">Sala</div>
            <div class="alert alert-default" role="alert">{{ session()->get('sala.nome') }}</div>
            <img src="{{ session()->get('sala.imagem') }}" width="50%">
        </div>

        <form method="POST" action="{{ route('login') }}" class="col-12 col-md-6 my-5 form">
        @else

        <form method="POST" action="{{ route('login') }}" class="col-12 col-md-6 offset-md-3 my-5 form">
        @endif

        @csrf
            <div class="alert alert-primary" role="alert">Autenticação</div>
            <div class="form-group">
                <label for="">Usuário</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus  placeholder="E-mail">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="">Senha</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••••••">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group form-row">
                <!-- toggle -->
                <div class="col mb-3 text-left">
                    <a href="{{ route('password.request') }}" class="recuperar-senha">esqueci minha senha</a>
                </div>
                <div class="col mb-3 text-left">
                    <a href="{{ route('cadastro_novo_medico') }}" class="recuperar-senha">cadastre-se</a>
                </div>
                <div class="col mb-3 text-right">
                    <span class="lembrar-me">
                        Lembrar usuário
                    </span>
                    <label class="switch">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-default btn-block">Login</button>
            </div>          
        </form>
    </div>
</div>
@endsection
