@extends('layouts.admin')

@section('content')
<h1 class="admin-title my-5">Uso por Sala</h1>

<div class="row my-5">
    <div class="col-12">

        <div class="card white admin px-5 pt-4 pb-2 mb-5">
            <h5 class="card-title">Resumo</h5>
            <ul class="list-inline resumo-valores">
                <li class="list-inline-item">Salas: <span class="valor">{{ $total_salas }}</span></li>
                <li class="list-inline-item">Total agendamentos: <span class="valor">{{ $qtd_agendamento }}</span></li>
                <li class="list-inline-item">Total bloqueios: <span class="valor">{{ $qtd_bloqueio }}</span></li>
                <li class="list-inline-item">Período: <span class="valor">{{ $filtro_inicio->format('d/m/Y') }} a {{ $filtro_fim->format('d/m/Y') }}</span></li>
            </ul>
        </div>

        <div class="card white admin px-5 pt-4 pb-2">
            <div class="row my-5">
                <div class="col-12 table-responsive">
                    <h5 class="title mb-5">Relatório de uso detalhado por sala</h5>

                    <form class="form" method="post">
                    @csrf
                        <div class="">
                            <div class="form-group form-row">
                                <div class="col-12 col-md-2">
                                    <select name="sala" class="form-control">
                                        <option value="">Selecione</option>
                                        @forelse ($salas_disponiveis as $sala)
                                        <option value="{{ $sala->id }}" @if ($filtro_salas == $sala->id) selected @endif>{{ $sala->numero }}-{{ $sala->nome }}</option>
                                        @empty
                                        <option value="">Nenhuma sala no período selecionado</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-12 col-md-2">
                                    <input type="text" name="inicio" class="form-control datePicker" placeholder="Data início" value="{{ $filtro_inicio->format('d/m/Y') }}">
                                </div>
                                <div class="col-12 col-md-2">
                                    <input type="text" name="fim" class="form-control datePicker" placeholder="Data fim" value="{{ $filtro_fim->format('d/m/Y') }}">
                                </div>
                                <div class="col-12 col-md-2">
                                    <button class="btn btn-default">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- datatables -->
                    <table class="display dtable">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Sala</th>
                                <th>Médico</th>
                                <th>Telefone</th>
                                <th>Horário</th>
                                <th>Valor</th>
                                <th>Tipo Pagamento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($salas as $u)

                            @if ($u->so_tipo == "BLOQUEIO")
                                <tr>
                                    <td>{{ \Carbon\Carbon::createfromformat('Y-m-d',$u->data)->format('d/m/Y') }}</td>
                                    <td>{{ $u->numero }}-{{ $u->nome }}</td>
                                    <td>{{ $u->name }} {{ $u->sobrenome }}</td>
                                    <td>{{ $u->telefone }}</td>
                                    <td>{{ \Carbon\Carbon::createfromformat('H:i:s',$u->hora)->format('H:i') }} - {{ \Carbon\Carbon::createfromformat('H:i:s',$u->hora)->addHour()->format('H:i') }}</td>
                                    <td>-</td>
                                    <td>Bloqueado</td>
                                </tr>
                            @else
                                <tr>
                                    <td>{{ \Carbon\Carbon::createfromformat('Y-m-d',$u->data)->format('d/m/Y') }}</td>
                                    <td>{{ $u->numero }}-{{ $u->nome }}</td>
                                    <td>{{ $u->name }} {{ $u->sobrenome }}</td>
                                    <td>{{ $u->telefone }}</td>
                                    <td>{{ \Carbon\Carbon::createfromformat('H:i:s',$u->hora)->format('H:i') }} - {{ \Carbon\Carbon::createfromformat('H:i:s',$u->hora)->addHour()->format('H:i') }}</td>
                                    <td>R$ {{ !empty($u->valor) ? number_format($u->valor,2,',','.') : number_format($u->valor_periodo,2,',','.') }}</td>
                                    <td>{{ !empty($u->tipo) ? ucwords($u->tipo) : "Utilização de créditos" }}</td>
                                </tr>
                            @endif

                            @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    Nenhuma reserva feita no período selecionado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <div>
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
