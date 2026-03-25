@extends('layouts.admin')

@section('content')
    <h1 class="admin-title my-5">Faturamento</h1>

    <div class="row my-5">
        <div class="col-12">

            <div class="card white admin px-5 pt-4 pb-2 mb-5">
                <h5 class="card-title">Resumo</h5>
                <ul class="list-inline resumo-valores">
                    <li class="list-inline-item">Medicos com agendamento: <span
                            class="valor">{{ count($total_medicos) }}</span></li>
                    <li class="list-inline-item">Total de agendamentos: <span class="valor">{{ count($faturamento) }}</span>
                    </li>
                    <li class="list-inline-item">Total recebido:<span class="valor">R$
                            {{ number_format($total_faturado, 2, ',', '.') }}</span></li>
                </ul>
            </div>

            <div class="card white admin px-5 pt-4 pb-2">
                <div class="row my-5">
                    <div class="col-12 table-responsive">
                        <h5 class="title mb-5">Relatório de faturamento</h5>

                        <form class="form" method="post">
                            @csrf
                            <div class="">
                                <div class="form-group form-row">
                                    <div class="col-12 col-md-2">
                                        <select name="sala" class="form-control">
                                            <option value="">Selecione</option>
                                            @forelse ($salas_disponiveis as $sala)
                                                <option value="{{ $sala->id }}"
                                                    @if ($filtro_salas == $sala->id) selected @endif>
                                                    {{ $sala->numero }}-{{ $sala->nome }}</option>
                                            @empty
                                                <option value="">Nenhuma sala no período selecionado</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <input type="text" name="inicio" class="form-control datePicker"
                                            placeholder="Data início" value="{{ $filtro_inicio->format('d/m/Y') }}">
                                    </div>
                                    <div class="col-12 col-md-2">
                                        @if ($filtro_fim != '')
                                            <input type="text" name="fim" class="form-control datePicker"
                                                placeholder="Data fim" value="{{ $filtro_fim->format('d/m/Y') }}">
                                        @else
                                            <input type="text" name="fim" class="form-control datePicker"
                                                placeholder="Data fim" value="{{ date('d/m/Y') }}">
                                        @endif
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
                                    <th>Tipo</th>
                                    <th>Sala</th>
                                    <th>Médico</th>
                                    <th>Valor</th>
                                    <th>Código Transação</th>
                                    <th>Modalidade</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($faturamento as $u)
                                    <tr>
                                        <td>
                                            @if (!empty($u->nome_sala))
                                                Reserva de horário
                                            @else
                                                Venda de crédito
                                            @endif
                                        </td>
                                        <td>{{ $u->nome_sala ?? $u->numero . '-' . $u->nome_sala }}</td>
                                        <td>{{ $u->name }} {{ $u->sobrenome }}</td>
                                        <td>{{ number_format($u->valor, 2, ',', '.') }}</td>
                                        <td>{{ $u->codigo_transacao ?? 'NI' }}</td>
                                        <td>{{ $u->tipo }}</td>
                                        <td>{{ \Carbon\Carbon::createfromformat('Y-m-d H:i:s', $u->created_at)->format('d/m/Y H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            Nenhum faturamento no período selecionado.
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
            <script></script>
        @endsection
