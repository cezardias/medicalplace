<?php

namespace App\Http\Controllers;

use App\User;
use App\Repositories\UsuariosRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{

    private $prefix_view = 'admin.cadastros';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usuarios_rep = new UsuariosRepository();
        return view($this->prefix_view.'.index',[
            'usuarios' => $usuarios_rep->getAll() 
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
            'usuario' => new User()
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
        $usuarios_rep = new UsuariosRepository();

        if (!$this->isCpfValid($request->get('cpf'))) {
            Session::flash('toastr', [
                'status' => 'warning', // success | info | warning | error
                'message' => 'CPF Inválido!']
            ); 
            return redirect()->route('usuario.create');  
        }

        $user_id = $usuarios_rep->grava($request);

        if (empty($user_id)) {
            Session::flash('toastr', [
                'status' => 'warning', // success | info | warning | error
                'message' => 'Erro ao cadastrar!']
            ); 
            return redirect()->route('usuario.create');
        }
        Session::flash('toastr', [
            'status' => 'success', // success | info | warning | error
            'message' => 'Gravado com sucesso!']
        ); 
        //return redirect()->route('usuario.index');
        return redirect()->route('usuario.edit',$user_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($user_id)
    {
        $User = new User();
        return view($this->prefix_view.'.edit',[
            'type' => 'update',
            'usuario' => $User->find($user_id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id)
    {

        $user_rep = new UsuariosRepository();

        if (!$this->isCpfValid($request->get('cpf'))) {
            Session::flash('toastr', [
                'status' => 'warning', // success | info | warning | error
                'message' => 'CPF Inválido!']
            ); 
            return redirect()->route('usuario.edit',$user_id);            
        }

        $gravou = $user_rep->grava($request,$user_id);

        if (empty($gravou)) {
            Session::flash('toastr', [
                'status' => 'warning', // success | info | warning | error
                'message' => 'Erro ao cadastrar!']
            ); 
            return redirect()->route('usuario.edit',$user_id);
        }
        Session::flash('toastr', [
            'status' => 'success', // success | info | warning | error
            'message' => 'Gravado com sucesso!']
        ); 
        //return redirect()->route('usuario.index');
        return redirect()->route('usuario.edit',$user_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user_rep = new UsuariosRepository();
        $user_rep->desativar($id);
        Session::flash('toastr', [
            'status' => 'success', // success | info | warning | error
            'message' => 'Usuário desativado.']
        ); 
        return redirect()->route('usuario.index');
    }

}
