<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\User;

class UsuariosRepository 
{

    private $model;
    public function __construct() {
        $this->model = new User();
    }

    public function getAll($tipo = null) {

        $where = [];
        $where[] = ['status','ativo'];
        if (!empty($tipo)) {
            $where[] = ['role',$tipo];
        }

        $users = DB::table('users')
            ->select('users.*')
            ->where($where)
            ->orderBy('name','asc')
            ->get();
        return $users;
    }

    public function grava($request, $id_user = null) {

        $user = $this->model;
        if (!empty($id_user)) {
            $user = $this->model->find($id_user);
        } else {
            $user->email = $request->get('email');
        }

        if (empty($id_user)) {
            $usuario_existe = $this->model->where('cpf',$request->get('cpf'))->orWhere('email',$request->get('email'))->first();
        } else {
            $usuario_existe = $this->model->where([['cpf',$request->get('cpf')],['id','!=',$id_user]])->orWhere([['email',$request->get('email')],['id','!=',$id_user]])->first();
        }

        if (!empty($usuario_existe))
            return false;

        $user->name = $request->get('nome');
        $user->sobrenome = $request->get('sobrenome');
        $user->role = $request->get('permissao');
        $user->telefone = $request->get('telefone');
        $user->cpf = $request->get('cpf');
        $user->status = 'ativo';

        if (!empty($request->get('senha')))
            $user->password = bcrypt($request->get('senha'));
        
        if (!$user->save()) {
            return false;
        } else {
            return $user->id;
        }
    }

    public function gravaMedico($request, $id_user = null) {

        $user = $this->model;
        if (!empty($id_user)) {
            $user = $this->model->find($id_user);
        } else {
            $user->email = $request->get('email');
        }

        $usuario_existe = $this->model->where('cpf',$request->get('cpf'))->orWhere('email',$request->get('email'))->first();
        if (!empty($usuario_existe))
            return false;

        $user->name = $request->get('nome');
        $user->sobrenome = $request->get('sobrenome');
        $user->role = 'medico';
        $user->telefone = $request->get('telefone');
        $user->cpf = $request->get('cpf');
        $user->status = 'ativo';

        if (!empty($request->get('senha')))
            $user->password = bcrypt($request->get('senha'));
        
        if (!$user->save()) {
            return false;
        } else {
            return $user->id;
        }

    }



    public function desativar($id) {
        $user = $this->model->find($id);
        $user->status = 'desativado';
        $user->save();
        return true;
    }


    public function totaisMedicos() {
        $totais = DB::select("
            SELECT COUNT(*) AS total,status FROM users WHERE role = 'medico' GROUP BY status
        ");

        $total_cadastrado = 0;
        foreach($totais as $k => $t) {
            $total_cadastrado = $total_cadastrado + $t->total;
        }

        $retorno['total_cadastrado'] = $total_cadastrado;
        $retorno['agrupado'] = $totais;
        return $retorno;
    }


}