@extends('layouts.admin')

@section('content')
    <h1 class="admin-title my-5">Dashboard</h1>
    <ul class="list-inline">
        <form method="post">
            @csrf
            <li class="list-inline-item">
                Período selecionado
            </li>
            <li class="list-inline-item">
                <div class="form-row form-group">
                    <div class="col">
                        <label for="inicio" class="sr-only">Início</label>
                        <input name="inicio" type="text" placeholder="Início" id="datepickerIni" class="form-control"
                            value="{{ $filtro_inicio->format('d/m/Y') }}">
                    </div>
                    <div class="col">

                        @if (!empty($filtro_fim))
                            <label for="final" class="sr-only">Final</label>
                            <input name="final" type="text" placeholder="Final" id="datepickerFim" class="form-control"
                                value="{{ $filtro_fim->format('d/m/Y') }}">
                        @else
                            <label for="final" class="sr-only">Final</label>
                            <input name="final" type="text" placeholder="Final" id="datepickerFim" class="form-control"
                                value="{{date('d/m/Y')}}">
                        @endif

                    </div>
                </div>
            </li>
            <li class="list-inline-item">
                <button class="btn btn-default">Filtrar</button>
            </li>
            <li class="list-inline-item">
                <a href="{{ route('admin.agendamento') }}" class="btn btn-default">Agendar agora</a>
            </li>
        </form>
    </ul>
    <div class="row my-5" id="adminResumo">
        <div class="col-12 col-md-1 text-center"></div>
        <div class="col-12 col-md-2 text-center">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $total_salas }}</h5>
                    salas<br />ativas
                </div>
            </div>
        </div>
        <!--<div class="col-12 col-md-2 text-center">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $total_horarios_livres }}</h5>
                                            horários<br />livres
                                        </div>
                                    </div>
                                </div>-->
        <div class="col-12 col-md-2 text-center">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $total_horarios_agendados }}</h5>
                    horários<br />agendados
                </div>
            </div>
        </div>
        <div class="col-12 col-md-2 text-center">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $medicos_agendados }}</h5>
                    médicos<br />agendados
                </div>
            </div>
        </div>

        <div class="col-12 col-md-2 text-center">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($online->faturamento, 2, ',', '.') }}</h5>
                    faturamento<br />online
                </div>
            </div>
        </div>
        <div class="col-12 col-md-2 text-center">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ number_format($presencial->faturamento, 2, ',', '.') }}</h5>
                    faturamento<br />presencial
                </div>
            </div>
        </div>
        <div class="col-12 col-md-1 text-center"></div>
    </div>

    <div class="row my-5">
        <div class="col-12 col-md-6 text-center">
            <div class="card ranking">
                <div class="card-body">
                    <h5 class="card-title">top 10 salas</h5>
                    <div class="container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nome da sala</th>
                                    <th>Qtd. Agendamentos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($top_salas as $s)
                                    <tr>
                                        <td>{{ $s->numero }}-{{ $s->nome }}</td>
                                        <td>{{ $s->total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">
                                            Nenhum Ranking
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 text-center">
            <div class="card ranking">
                <div class="card-body">
                    <h5 class="card-title">top 10 médicos</h5>
                    <div class="container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nome do médico</th>
                                    <th>Qtd. Agendamentos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($top_medicos as $s)
                                    <tr>
                                        <td>{{ $s->name }} {{ $s->sobrenome }}</td>
                                        <td>{{ $s->total }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">
                                            Nenhum Ranking
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
