<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Secretaria
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
        if (Auth::user() && Auth::user()->role == 'secretaria' && Auth::user()->status == 'ativo') {
            return $next($request);
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
