<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Webhook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('administrador');
    }

    public function index()
    {
        $webhooks = Webhook::all();
        $tokens = Auth::user()->tokens;
        
        return view('admin.api.index', compact('webhooks', 'tokens'));
    }

    public function generateToken(Request $request)
    {
        $tokenName = $request->input('name', 'API Token');
        $token = Auth::user()->createToken($tokenName);

        return back()->with('toastr', [
            'status' => 'success',
            'message' => 'Token gerado com sucesso: ' . $token->plainTextToken . ' (Copie agora, ele não será exibido novamente!)'
        ]);
    }

    public function revokeToken(Request $request, $id)
    {
        Auth::user()->tokens()->where('id', $id)->delete();

        return back()->with('toastr', [
            'status' => 'success',
            'message' => 'Token revogado com sucesso!'
        ]);
    }

    public function storeWebhook(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'event' => 'required'
        ]);

        Webhook::create([
            'url' => $request->url,
            'event' => $request->event,
            'secret' => Str::random(32),
            'status' => 'active'
        ]);

        return back()->with('toastr', [
            'status' => 'success',
            'message' => 'Webhook cadastrado com sucesso!'
        ]);
    }

    public function deleteWebhook($id)
    {
        Webhook::destroy($id);

        return back()->with('toastr', [
            'status' => 'success',
            'message' => 'Webhook removido com sucesso!'
        ]);
    }

    public function testWebhook(Request $request, $id)
    {
        $webhook = Webhook::findOrFail($id);
        $data = [
            'test' => true,
            'message' => 'Este é um disparo de teste do MedicalPlace',
            'timestamp' => now()->toDateTimeString()
        ];

        \App\Jobs\DispatchWebhook::dispatch($webhook, $data);

        return back()->with('toastr', [
            'status' => 'info',
            'message' => 'Disparo de teste enviado para a fila!'
        ]);
    }
}
