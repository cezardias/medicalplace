<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\Session;

use App\Salas;
use App\SalasImagens;

class Medico
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (Auth::user() &&  Auth::user()->role == 'medico' && Auth::user()->status == 'ativo') {
            return $next($request);
        }

        if (Auth::user() &&  Auth::user()->role == 'medico' && Auth::user()->status == 'inativo') {
            Auth::logout();
            Session::flash('toastr', [
                'status' => 'warning', // success | info | warning | error
                'message' => 'Conta inativada. Entre em contato com o suporte.']
            ); 
        }
        
        // Se chegou até aqui é médico e pode estar tentando ver uma sala
        $url = explode('/',$request->getRequestUri());
        if (!empty($url[2])) {
            $salas_model = new Salas();
            $sala = $salas_model->where('slug',$url[2])->first();
            if (!empty($sala)) {
                $salas_model = new SalasImagens();
                $imagem = $salas_model->where('sala_id',$sala->id)->first();
                Session::put('sala',array(
                    'nome' => $sala->nome,
                    'slug' => $sala->slug,
                    'imagem' => !empty($imagem) ? $imagem->caminho : null
                ));
            }
        }

        if (Auth::user()) {
             if (Auth::user()->role == 'administrador' || Auth::user()->role == 'secretaria') {
                return redirect()->guest(route('admin.index'));
            }
            return redirect()->guest(route('minha_conta'));
        }
        return redirect()->route('login');
    }
}
