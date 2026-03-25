<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function directUser()
    {
        if (Auth::user()->role == 'administrador' || Auth::user()->role == 'secretaria') {
            return redirect()->route('admin.index');
        }
        // qualquer outro caso volta para área pública Médico ou outra coisa
        
        if (!empty(Session::has('sala')))
            return redirect()->route('ver_sala',['slug'=>Session::get('sala.slug')]);

        return redirect()->route('minha_conta');
    }
}
