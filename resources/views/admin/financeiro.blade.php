@extends('layouts.admin')

@section('content')
<h1 class="admin-title my-5">Financeiro Salas</h1>

<div class="container my-5" id="contentWrapper">
    <div class="row text-left">
        @forelse ($salas as $sala)
        <div class="col-12 col-md-4 pb-3">
            <div class="card">
                <img class="card-img-top" src="{{ $sala->caminho }}" title="{{ $sala->nome }}">
                <div class="card-body">
                    <h3 class="card-title title">{{ $sala->nome }}</h3>
                    <p class="card-text lead">R$ {{ number_format($sala->total,2,',','.') }}</p>
                </div>
            </div>
        </div>
        @empty

        @endforelse
    </div>
</div>

@endsection

@section('style')
<style>

</style>
@endsection

@section('javascript')
<script>

</script>
@endsection