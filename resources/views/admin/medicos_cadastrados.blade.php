@extends('layouts.admin')

@section('content')
<h1 class="admin-title my-5">Médicos Cadastrados</h1>

<div class="row my-5">
    <div class="col-12">


        <div class="card white admin px-5 pt-4 pb-2 mb-5">
            <h5 class="card-title">Resumo</h5>
            <ul class="list-inline resumo-valores">
            <li class="list-inline-item">Médicos cadastrados: <span class="valor">{{ $totais['total_cadastrado'] }}</span></li>
            @foreach ($totais['agrupado'] as $g)
            <li class="list-inline-item">Médicos {{ $g->status }}s: <span class="valor">{{ $g->total }}</span></li>
            @endforeach
            </ul>
        </div>


        <div class="card white admin px-5 pt-4 pb-2">
            <div class="row my-5">
                <div class="col-12 table-responsive">
                    <h5 class="title mb-5">Relatório de médicos cadastrados</h5>

                    <form method="post" class="form">
                    @csrf
                        <div class="">
                            <div class="form-group form-row">
                                <div class="col-12 col-md-2">
                                    <select name="medico" class="form-control">
                                        <option value="">Selecione o Médico</option>
                                        @foreach ($medicos as $medico)
                                            <option value="{{ $medico->id }}" @if ($filtro_medico == $medico->id) selected @endif>{{ $medico->name }} {{ $medico->sobrenome }}</option>
                                        @endforeach
                                    </select>
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
                                <th>Data Cadastro</th>
                                <th>Médico</th>
                                <th>Telefone</th>
                                <th>Email</th>
                                <th>Qtd. Agend.</th>
                                <th>Último Agend.</th>
                                <th>Status</th>
                                <th>Editar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($medicos as $u)
                            <tr>
                                <td>{{ \Carbon\Carbon::createfromformat('Y-m-d H:i:s',$u->created_at)->format('d/m/Y') }}</td>
                                <td>{{ $u->name }} {{ $u->sobrenome }}</td>
                                <td>{{ $u->telefone }}</td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->qt }}</td>
                                <td>@if (!empty($u->ultimo_agendamento)) {{ \Carbon\Carbon::createfromformat('Y-m-d H:i:s',$u->ultimo_agendamento)->format('d/m/Y H:i') }} @else Não há @endif</td>
                                <td>{{ $u->status }}</td>
                                <td><a href="{{ route('usuario.edit',[ $u->id ]) }}"><i class="fa fa-pencil" aria-hidden="true"></i></a></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    Nenhum médico cadastrado. <a href="{{ route('usuario.create') }}">Clique aqui</a> e cadastre um médico.
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
