<?php

namespace App\Http\Controllers;

use App\Salas;
use App\SalasImagens;
use App\Repositories\SalasRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SalasController extends Controller
{
    private $prefix_view = 'admin.salas';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sala_rep = new SalasRepository();
        return view($this->prefix_view.'.index',[
            'salas' => $sala_rep->getTodas() 
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->prefix_view.'.edit',[
            'type' => 'new',
            'sala' => new Salas()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sala_rep = new SalasRepository();
        $sala_id = $sala_rep->grava($request);

        if (!empty($sala_id)) {
            if (!empty($request->file('imagens')))
                $this->gravaImagensSala($request->file('imagens'),$sala_id);
        } else {
            Session::flash('toastr', [
                'status' => 'warning', // success | info | warning | error
                'message' => 'Erro ao gravar!']
            ); 
            return redirect()->route('salas.edit',$sala_id);
        }
        Session::flash('toastr', [
            'status' => 'success', // success | info | warning | error
            'message' => 'Gravado com sucesso!']
        ); 
        return redirect()->route('salas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Salas  $salas
     * @return \Illuminate\Http\Response
     */
    public function show(Salas $salas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Salas  $salas
     * @return \Illuminate\Http\Response
     */
    public function edit($sala_id)
    {
        $salas = new Salas();
        return view($this->prefix_view.'.edit',[
            'type' => 'update',
            'sala' => $salas->find($sala_id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Salas  $salas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $sala_id)
    {
        $sala_rep = new SalasRepository();
        $sala_id = $sala_rep->grava($request,$sala_id);

        if (!empty($sala_id)) {
            if (!empty($request->file('imagens')))
                $this->gravaImagensSala($request->file('imagens'),$sala_id);
        } else {
            Session::flash('toastr', [
                'status' => 'warning', // success | info | warning | error
                'message' => 'Erro ao gravar!']
            ); 
            return redirect()->route('salas.edit',$sala_id);
        }
        Session::flash('toastr', [
            'status' => 'success', // success | info | warning | error
            'message' => 'Atualizado com sucesso!']
        ); 
        return redirect()->route('salas.edit',$sala_id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Salas  $salas
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_sala)
    {
        $sala_rep = new SalasRepository();
        $sala_rep->desativar($id_sala);
        Session::flash('toastr', [
            'status' => 'success', // success | info | warning | error
            'message' => 'Sala desativada.']
        ); 
        return redirect()->route('salas.index');
    }


    public function apagarImagemSala(Request $request) {
        $imagem = SalasImagens::find($request->get('id'));
        if ($imagem->delete()) {
            Session::flash('toastr', [
                'status' => 'success', // success | info | warning | error
                'message' => 'Imagem apagada com sucesso']
            ); 
        } else {
            Session::flash('toastr', [
                'status' => 'warning', // success | info | warning | error
                'message' => 'Erro ao apagar imagem']
            ); 
        }
    }


}
