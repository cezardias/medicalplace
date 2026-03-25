@extends('layouts.admin')

@section('content')
<h1 class="admin-title my-5">Agenda {{ \Carbon\Carbon::createfromformat('Y-m-d H:i:s',$data)->format('d/m/Y') }}</h1>


@foreach ($salas as $s)
    <label for=""><h3>{{ $s->nome }}</h3></label>
    <div class="sala-agenda">
        <div class="mb-3">
            @foreach ($horarios as $h)
                @if (!empty($s->reservas[$h.":00"]))
                    <button class="btn btn-primary mx-2 my-2" data-toggle="tooltip" data-placement="top" title='{{ $s->reservas[$h.":00"]["tipo"] }}: {{ $s->reservas[$h.":00"]["usuario"] }}'>{{ $h }}</button>
                @else
                    <button class="btn btn-success mx-2 my-2">{{ $h }}</button>
                @endif
            @endforeach
        </div>
    </div>
@endforeach


@endsection

@section('style')
<style>

</style>
@endsection

@section('javascript')
<script>

</script>
@endsection