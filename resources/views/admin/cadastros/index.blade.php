@extends('layouts.admin')

@section('content')

{{-- dd($usuarios) --}}

<h1 class="admin-title my-5">Cadastros</h1>

<ul class="list-inline">
    <li class="list-inline-item">
        <a href="{{ route('usuario.create') }}" class="btn btn-default-outline"><i class="fa fa-plus"></i> Cadastrar novo usuário</a>
    </li>
</ul>

<div class="row my-5">
    <div class="col-12">
        <div class="card white admin px-5 pt-4 pb-2">
            <div class="row my-5">
                <div class="col-12 table-responsive">
                    <!--h5 class="title mb-5">Relatório de uso detalhado por sala</h5-->
                    <!-- datatables -->
                    <table class="display dtable">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Nome</th>
                                <th>User</th>
                                <th>Tipo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($usuarios as $u)
                            <tr>
                                <td>{{ $u->id }}</td>
                                <td>{{ $u->name }} {{ $u->sobrenome }}</td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->role }}</td>
                                <td>
                                    <a href="{{ route('usuario.edit',[ $u->id ]) }}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('delete_{{ $u->id }}').submit();"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    <form action="{{ route('usuario.destroy',[ $u->id ]) }}" method="post" id="delete_{{ $u->id }}">
                                        {{ method_field('DELETE') }}
                                        @csrf
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Nenhuma sala disponível. <a href="{{ route('salas.create') }}">Clique aqui</a> e cadastre uma sala.
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