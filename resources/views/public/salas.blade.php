@extends('layouts.public')

@section('content')
<!-- HOME -->
<div class="container my-5" id="contentWrapper">
    <div class="row text-center">
        <div class="col-12">
            <h1 class="title">Salas</h1>
            <p class="subtitle">Uma clínica que visa o bem estar de seus clientes, proporcionando um ambiente agradável e aconchegante. Venha conhecer!</p>
        </div>
        <div class="col-12 col-md-8 offset-md-4 my-5">
        <form method="post" action="{{ route('home') }}">
            @csrf
            <div class="form-row">
                <div class="input-group col-md-2">
                    <select class="form-control" name="horario">
                        <option value="">Horário</option>
                        @foreach ($horarios as $h)
                            <option value="{{ $h }}" @if($h == $horario_busca) selected @endif>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group col-md-2">
                    <input class="form-control py-2 border-right-0 border datePicker" type="search" placeholder="Data" name="data" id="data" value="{{ $data_busca }}">
                </div>
                <div class="input-group col-md-8">
                    <input class="form-control py-2 border-right-0 border" type="search" placeholder="Busque pelo nome ou número da sala" value="{{ $termo_busca }}" name="termo" id="example-search-input">
                    <span class="input-group-append">
                    <button class="btn btn-outline-secondary border-left-0 border" type="submit">
                            <i class="fa fa-search"></i>
                    </button>
                    </span>
                </div>
            </div>
        </form>
        </div>
    </div>
    <div class="row text-left">
        @forelse ($salas as $sala)
        <div class="col-12 col-md-4 pb-5">
            <a href="{{ route('ver_sala',[ $sala->slug ]) }}">
                <div class="card">
                    <span class="float-btn">
                        <span class="zoom-in">
                            <i class="fa fa-search-plus"></i>
                        </span>
                    </span>
                    <img class="card-img-top" src="{{ $sala->capa }}" alt="Card image cap">
                    <div class="card-body">
                        <h3 class="card-title title">{{ $sala->nome }} <small class="ml-5 pl-5"><span class="sala-numero">{{ $sala->numero }}</span></small></h3>
                        <span class="card-text lead">{!! $sala->descricao !!}</span>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 col-md-12 pb-5">
            <div class="alert alert-warning" role="alert">
                Nenhum sala encontrada.
            </div>
        </div>
        @endforelse
        <div class="col-12 my-5 text-center">
            <button class="btn btn-default-outline show-more"   style="display:none;">Carregar mais resultados</button>
            <button class="btn btn-default-outline show-less" style="display:none;">Carregar menos resultados</button>
        </div>
    </div>
</div>
<!-- HOME  -->
@endsection

@section('javascript')
<script>
    var cards = $('.card').length

    if (cards > 16) {
        $('.show-more').show()
    }

    $('.show-more').click( function () {
        $('.card').show();
        $(this).hide()
        $('.show-less').show()
    })

    $('.show-less').click( function () {
        $('.card').slice(3).hide();
        $(this).hide()
        $('.show-more').show()
    })

</script>
@endsection
