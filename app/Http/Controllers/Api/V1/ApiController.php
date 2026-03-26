<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Especialidade;
use App\Servico;
use App\Lead;
use App\Unidade;
use App\Salas;
use App\SalasOcorrencias;
use App\Repositories\SalasOcorrenciasRepository;
use App\Repositories\SalasRepository;
use App\Helpers\WebhookHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Module 1: Specialties and Services
     */
    public function getEspecialidades()
    {
        $especialidades = Especialidade::where('status', 'ativa')->get();
        return response()->json(['status' => 'success', 'data' => $especialidades]);
    }

    public function getServicos($esp_id)
    {
        $servicos = Servico::where('especialidade_id', $esp_id)->where('status', 'ativa')->get();
        return response()->json(['status' => 'success', 'data' => $servicos]);
    }

    /**
     * Module 2: Leads
     */
    public function postLeadPaciente(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string',
            'telefone' => 'required|string',
            'email' => 'nullable|email',
            'cpf' => 'nullable|string',
            'convenio' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $lead = Lead::create(array_merge($request->all(), ['tipo' => 'paciente']));
        
        WebhookHelper::dispatch('lead.paciente.created', $lead->toArray());

        return response()->json(['status' => 'success', 'message' => 'Lead de paciente registrado com sucesso', 'data' => $lead]);
    }

    public function postLeadMedico(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string',
            'telefone' => 'required|string',
            'crm' => 'required|string',
            'especialidade' => 'nullable|string',
            'turno' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $lead = Lead::create(array_merge($request->all(), ['tipo' => 'medico']));

        WebhookHelper::dispatch('lead.medico.created', $lead->toArray());

        return response()->json(['status' => 'success', 'message' => 'Lead de médico registrado com sucesso', 'data' => $lead]);
    }

    /**
     * Module 3: Agenda and Availability
     */
    public function getDisponibilidade(Request $request)
    {
        $termo = $request->get('termo');
        $data = $request->get('data'); // d/m/Y
        $horario = $request->get('horario'); // HH:mm

        $salas_rep = new SalasRepository();
        $salas = $salas_rep->getSalas($termo, $data, $horario);

        return response()->json(['status' => 'success', 'data' => $salas]);
    }

    public function reservar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sala_id' => 'required|exists:salas,id',
            'data' => 'required|date_format:d/m/Y',
            'hora' => 'required',
            'lead_id' => 'nullable|exists:leads,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $data = Carbon::createFromFormat('d/m/Y', $request->get('data'));
        $hora = $request->get('hora');
        
        // Bloqueio temporário
        $reserva = new SalasOcorrencias();
        $reserva->sala_id = $request->get('sala_id');
        $reserva->tipo = 'reserva_temporaria';
        $reserva->data = $data->format('Y-m-d');
        $reserva->hora = $hora . ":00";
        $reserva->status = 'reservado';
        $reserva->save();

        return response()->json([
            'status' => 'success', 
            'message' => 'Horário bloqueado temporariamente', 
            'reserva_id' => $reserva->id
        ]);
    }

    public function confirmar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reserva_id' => 'required|exists:salas_ocorrencias,id',
            'google_event_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $reserva = SalasOcorrencias::find($request->get('reserva_id'));
        $reserva->status = 'confirmado';
        $reserva->google_event_id = $request->get('google_event_id');
        $reserva->save();

        return response()->json(['status' => 'success', 'message' => 'Reserva confirmada com sucesso']);
    }

    /**
     * Module 4: Knowledge Base and Units
     */
    public function getRegrasLocacao()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'termos' => 'Regras vigentes para locação...',
                'cancelamento' => 'Cancelamentos permitidos até 24h antes.'
            ]
        ]);
    }

    public function getUnidades()
    {
        $unidades = Unidade::where('status', 'ativa')->get();
        return response()->json(['status' => 'success', 'data' => $unidades]);
    }
}
