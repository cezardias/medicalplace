<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Salas;
use App\Agendamento;
use Carbon\Carbon;

class PublicApiController extends Controller
{
    public function getSalas()
    {
        $salas = Salas::all();
        return response()->json([
            'status' => 'success',
            'data' => $salas
        ]);
    }

    public function getAgenda(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        
        $agendamentos = Agendamento::with('medico', 'sala')
            ->whereDate('data_reserva', $date)
            ->get();

        return response()->json([
            'status' => 'success',
            'date' => $date,
            'data' => $agendamentos
        ]);
    }
}
