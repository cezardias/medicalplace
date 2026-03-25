<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Administrador
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
        if (Auth::user() && in_array(Auth::user()->role,['administrador','secretaria']) && Auth::user()->status == 'ativo') {
            return $next($request);
        }
        return redirect()->route('login');
        //return redirect('home')->with('error','You have not admin access');
    }
}
