@extends('layouts.admin')

@section('javascript')
    <script src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>
    <script>
        $(function () {

            CKEDITOR.replace( 'descricao' );

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $('.apagar-imagem').on('click',function() {
                $.ajax({
                    url: "{{ route('admin.apagar_imagem_sala') }}",
                    method: "POST",
                    data: { id : $(this).data('id') },
                    complete: function(response) {
                        location.reload(true);
                        /*
                        let retorno = jQuery.parseJSON(response.responseText);
                        if (retorno.status == false) {
                            toastr["warning"](retorno.message);
                        } else {
                            toastr["success"](retorno.message);
                        }
                        $('#exampleModalCenter').modal('toggle');
                        */
                    }
                });
            });

        });
    </script>
@endsection

@section('content')
<h1 class="admin-title my-5">Cadastros</h1>
<div class="row my-5">
    <div class="col-12">
        <div class="card white admin px-5 pt-4 pb-2">
            <div class="row my-5">
                <div class="col-12">
                    <h5 class="title mb-5">Cadastrar nova sala</h5>

                    @if ($type == 'update')
                        <form action="{{ route('salas.update',[ $sala->id ]) }}" method="post" enctype="multipart/form-data">
                        {{ method_field('PUT') }}
                    @else
                        <form action="{{ route('salas.store') }}" method="post" enctype="multipart/form-data">
                    @endif

                    @csrf
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group form-row">
                                    <div class="col-12 col-md-4">
                                        <label for="numero">Número</label>
                                        <input name="numero" type="text" class="form-control" placeholder="número" value="{{ $sala->numero }}">
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <label for="nome">Nome da sala</label>
                                        <input name="nome" type="text" class="form-control" placeholder="nome da sala" value="{{ $sala->nome }}">
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-12">
                                        <label for="descricao">Descrição</label>
                                        <textarea name="descricao" id="descricao" class="form-control" id="descricao" cols="30" rows="5">
                                            {{ $sala->descricao }}
                                        </textarea>
                                    </div>
                                </div>
                                {{--
                                <div class="form-group">
                                    <label for="">Selecion horários</label>
                                    <div class="sala-agenda">
                                        <div class="mb-3">
                                            <button class="btn btn-default-outline mx-2 my-2">07:00</button>
                                            <button class="btn btn-default-outline mx-2 my-2">07:00</button>
                                            <button class="btn btn-default-outline mx-2 my-2">07:00</button>
                                            <button class="btn btn-default-outline mx-2 my-2">07:00</button>
                                            <button class="btn btn-default-outline mx-2 my-2">07:00</button>
                                            <button class="btn btn-default-outline mx-2 my-2">07:00</button>
                                            <button class="btn btn-default-outline mx-2 my-2">07:00</button>
                                            <button class="btn btn-default-outline mx-2 my-2" disabled >07:00</button>
                                            <button class="btn btn-default-outline mx-2 my-2">07:00</button>
                                            <button class="btn btn-default-outline mx-2 my-2">07:00</button>
                                            <button class="btn btn-default-outline mx-2 my-2">07:00</button>
                                            <button class="btn btn-default-outline mx-2 my-2">07:00</button>
                                        </div>
                                    </div>
                                </div>
                                --}}
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group form-row mt-2">
                                    <div class="col-12 col-md-6">
                                        <label for="sala">Período</label>
                                        <input type="text" name="data_inicial" class="form-control datePicker" placeholder="data inicial" value="@if (!empty($sala->data_inicial)) {{ \Carbon\Carbon::createFromFormat('Y-m-d',$sala->data_inicial)->format('d/m/Y') }} @endif">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="sala">&nbsp;</label>
                                        <input type="text" name="data_final" class="form-control datePicker" placeholder="data final" value="@if (!empty($sala->data_final)) {{ \Carbon\Carbon::createFromFormat('Y-m-d',$sala->data_final)->format('d/m/Y') }} @endif">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Valor de um período</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">R$</div>
                                        </div>
                                        <input type="text" name="valor_periodo" class="form-control moeda" placeholder="Valor" value="{{ number_format($sala->valor_periodo,2) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @forelse ($sala->imagens as $imagem)
                                <div class="col-12 col-md-3 p-2">
                                    <div class="card">
                                        <img src="{{ asset($imagem->caminho) }}" class="card-img-top">
                                        <div class="card-body">
                                            <button type="button" class="btn btn-danger apagar-imagem float-right" data-id="{{ $imagem->id }}">Apagar</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 p-2">
                                    <h1 class="display-5">Nenhuma imagem</h1>
                                </div>
                            @endforelse
                        </div>
                        <div class="row">
                            <div class="col-12 p-2">
                                <label for="inputImagem">
                                    <input type="file" id="inputImagem" name="imagens[]" class="form-control d-none" multiple>
                                    <h3><i class="fa fa-upload"></i>Upload Nova Imagem</h3>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 p-2">
                                <div class="form-group mt-5 pt-4">
                                    <button type="submit" class="btn btn-default float-right">Cadastrar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <div>
</div>
@endsection
