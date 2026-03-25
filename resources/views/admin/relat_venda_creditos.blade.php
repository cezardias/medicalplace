@extends('layouts.admin')

@section('content')
    <h1 class="admin-title my-5">Venda de créditos</h1>

    <div class="row my-5">
        <div class="col-12">

            <div class="card white admin px-5 pt-4 pb-2 mb-5">
                <h5 class="card-title">Resumo</h5>
                <ul class="list-inline resumo-valores">
                    <li class="list-inline-item">Total creditado: <span class="valor">R$
                            {{ number_format($valor_creditado, 2, ',', '.') }}</span></li>
                    <li class="list-inline-item">Total cobrado:<span class="valor">R$
                            {{ number_format($valor_cobrado, 2, ',', '.') }}</span></li>
                </ul>
            </div>

            <div class="card white admin px-5 pt-4 pb-2">
                <div class="row my-5">
                    <div class="col-12 table-responsive">
                        <h5 class="title mb-5">Relatório de venda de créditos</h5>
                        <form class="form" method="post">
                            @csrf
                            <div class="">
                                <div class="form-group form-row">
                                    <div class="col-12 col-md-2">
                                        <select name="medico" class="form-control">
                                            <option value="">Selecione</option>
                                            @foreach ($medicos as $medico)
                                                <option value="{{ $medico->id }}"
                                                    @if ($medico->id == $filtro_medico) selected @endif>{{ $medico->name }}
                                                    {{ $medico->sobrenome }}</option>
                                            @endforeach
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
                                    <th>Data do crédito</th>
                                    <th>Médico</th>
                                    <th>CPF</th>
                                    <th>Valor creditado</th>
                                    <th>Valor cobrado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($creditos as $c)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::createfromformat('Y-m-d H:i:s', $c->created_at)->format('d/m/Y H:i:s') }}
                                        </td>
                                        <td>{{ $c->name }} {{ $c->sobrenome }} </td>
                                        <td>{{ $c->cpf }}</td>
                                        <td>{{ number_format($c->valor, 2, ',', '.') }}</td>
                                        <td>{{ number_format($c->valor_cobrado, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            Nenhum crédito no período selecionado.
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
