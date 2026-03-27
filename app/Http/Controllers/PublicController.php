<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use Log;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Mail\ConfirmacaoAgendamento;
use App\Mail\CancelamentoAgendamento;

use App\PagSeguro;
use App\Salas;
use App\User;
use App\UsersCards;

use App\Repositories\TransacoesRepository;
use App\Repositories\UsersCardsRepository;
use App\Repositories\SalasRepository;
use App\Repositories\SalasOcorrenciasRepository;
use App\Repositories\CreditosRepository;
use App\Repositories\UsuariosRepository;

class PublicController extends Controller
{

    /**
     * Listagem e busca de salas.
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $termo_busca = null;
        if (!empty($request->get('termo'))) {
            $termo_busca = $request->get('termo');
        }

        $data_busca = null;
        if (!empty($request->get('data'))) {
            $data_busca = $request->get('data');
        }

        $horario_busca = null;
        if (!empty($request->get('horario'))) {
            $horario_busca = $request->get('horario');
        }

        $salas_ocorrencias_rep = new SalasOcorrenciasRepository();


        $salas_model = new SalasRepository();
        $salas = $salas_model->getSalas($termo_busca, $data_busca, $horario_busca);

        return view('public.salas', [
            'salas' => $salas,
            'termo_busca' => $termo_busca,
            'data_busca' => $data_busca,
            'horario_busca' => $horario_busca,
            'horarios' => $salas_ocorrencias_rep->horarios_funcionamento
        ]);
    }

    /**
     * Visualizar sala
     */
    public function verSala(Request $request, $slug)
    {

        if (!empty(Session::has('sala')))
            Session::forget('sala');

        $salas_model = new Salas();
        $sala = $salas_model->where('slug', $slug)->first();

        $data_inicial = Carbon::today();
        $data = Carbon::today();
        if (!empty($request->get('data_selecionada'))) {
            $data = Carbon::createFromFormat('d/m/Y', $request->get('data_selecionada'));
        }

        // dia mínimo e horários desse dia
        $data_inicial = Carbon::today();
        $ocorrencias_rep = new SalasOcorrenciasRepository();
        $ocorrencias = $ocorrencias_rep->getOcorrencias($data->format('Y-m-d 00:00:00'), $sala->id);

        $agora = Carbon::now();
        $ocorrencias_trat = $ocorrencias;

        return view('public.sala', [
            'sala' => $sala,
            'horarios' => $ocorrencias_rep->getHorariosFuncionamento(),
            'ocorrencias' => $ocorrencias_trat,
            'data_inicial' => $data_inicial,
            'data' => $data,
            'agora' => $agora
        ]);
    }

    public function preCheckoutAgendamento(Request $request)
    {
        if (
            !empty($request->get('sala')) &&
            !empty($request->get('data_agendamento')) &&
            !empty($request->get('horario'))
        ) {

            Session::put('agendamento', array(
                'sala' => $request->get('sala'),
                'data_agendamento' => $request->get('data_agendamento'),
                'horario' => $request->get('horario')
            ));
        }



    }


    public function checkoutAgendamento(Request $request)
    {
        $raw = $request->get('horarios_selecionados');
        $old = $request->get('horario');
        
        $horario_selecionado = array();
        
        if (!empty($raw)) {
            $horario_selecionado = explode(',', $raw);
        } elseif (is_array($old)) {
            foreach($old as $h => $v) {
                if ($v == 1 || $v == "1") {
                    $horario_selecionado[] = $h;
                }
            }
        }

        $salas_rep = new SalasRepository();
        $sala = $salas_rep->getSala($request->get('sala'));

        if (empty($horario_selecionado)) {
            \Log::warning("Agendamento sem horários: ", $request->all());
        }

        $valor_total = $sala->valor_periodo * count($horario_selecionado);

        $cards_rep = new UsersCardsRepository();
        $creditos_rep = new CreditosRepository();

        return view('public.checkout', [
            'sala' => $sala,
            'data_agendamento' => $request->get('data_agendamento'),
            'horario_selecionado' => $horario_selecionado,
            'valor_total' => $valor_total,
            'cartoes_cadastrados' => $cards_rep->getMeusCartoes(Auth::user()->id),
            'creditos' => $creditos_rep->getExtrato(Auth::user()->id)
        ]);
    }

    public function testSendEmail()
    {

        try {
            $params = array('name' => "Virat Gandhi");
            Mail::send(['text' => 'mail'], $params, function ($message) {
                $message->to('marcelo_sagayama@hotmail.com', 'Marcelo Sagayama')
                    ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                    ->subject('Agendamento confirmado');
            });
        } catch (\Exception $e) {
            Log::debug($e);
        }
    }


    public function checkoutPagamento(Request $request)
    {

        $teste_log = array();

        $teste_log['inicio'] = '-----------------------------------';
        $cartao = null;
        if (!empty($request->get('card_id'))) {
            $cartao = UsersCards::find($request->get('card_id'));
        }

        $salas_rep = new SalasRepository();
        $sala = $salas_rep->getSala($request->get('sala'));
        $valor_total = $sala->valor_periodo * count($request->get('horario'));

        $creditos_rep = new CreditosRepository();
        $creditos_usuario = $creditos_rep->getExtrato(Auth::user()->id);

        $credito_selecionado = 0;
        if (!empty($request->get('credito_selecionado'))) {
            $credito_selecionado = number_format((float) $request->get('credito_selecionado'), 2, '.', ',');
            if ($credito_selecionado > $creditos_usuario['saldo']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Créditos inválidos'
                ]);
            }
            $valor_total = $valor_total - $credito_selecionado;
        }

        if ($valor_total < 0) {
            return response()->json([
                'status' => false,
                'message' => 'Valores inseridos não conferem. Tente novamente.'
            ]);
        }

        if (empty($request->get('horario'))) {
            return response()->json([
                'status' => false,
                'message' => 'Horário selecionado inválido.'
            ]);
        }

        if (count($request->get('horario')) === 0) {
            return response()->json([
                'status' => false,
                'message' => 'Horário selecionado inválido.'
            ]);
        }


        $ocorrencias_rep = new SalasOcorrenciasRepository();
        $data = Carbon::createFromFormat('d/m/Y', $request->get('data_agendamento'));
        $horarios = $request->get('horario');

        $ocorrencias_rep = new SalasOcorrenciasRepository();
        $horarios_disponiveis = $ocorrencias_rep->getHorariosFuncionamento();
        $horarios_ocupados = $ocorrencias_rep->getOcorrencias($data->format('Y-m-d 00:00:00'), $request->get('sala'));

        foreach ($horarios as $hora) {
            if (!in_array($hora, $horarios_disponiveis) || in_array($hora, $horarios_ocupados)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Horário selecionado inválido.'
                ]);
            }
        }

        $teste_log['horarios'] = $horarios;
        $teste_log['valor_total'] = $valor_total;
        $pagamento = [];
        if ($valor_total > 0) {
            $pagseguro = new Pagseguro();
            $pagamento = $pagseguro->charge($request, $valor_total, $cartao, Auth::user()->id);
            /*$pagamento['status'] = true;

            $dados = (object)[];
            $dados->id = 1;
            $dados->reference_id = 1;


            $dados->amount = (object)[];
            $dados->amount->value = 35;

            $dados->payment_response = (object)[];
            $dados->payment_response->code = 2000;

            $dados->payment_method = (object)[];
            $dados->payment_method->card = 123;

            $pagamento['retorno'] = $dados;
            $pagamento['mensagem'] = 'teste ok';*/
        } else {
            $pagamento['status'] = true;
        }

        $teste_log['pagamento'] = $pagamento;
        if ($pagamento['status'] == true) {
            $transacao = null;
            if ($valor_total > 0) {
                $transacoes_rep = new TransacoesRepository();
                $transacao = $transacoes_rep->createOnline($pagamento['retorno'], $sala->id, Auth::user()->id);
                if (!empty($request->get('gravar_cartao'))) {
                    $cards_rep = new UsersCardsRepository();
                    $cards_rep->create($pagamento['retorno']->payment_method->card, Auth::user()->id);
                }
            }

            if ($credito_selecionado > 0) {
                $credito_rep = new CreditosRepository();
                $credito_rep->grava(Auth::user()->id, $credito_selecionado, 'debito');
            }

            foreach ($horarios as $hora) {
                $teste_log['gravaOcorrencia'] = 'Sala:' . $request->get('sala') . ', consulta, data:' . $data . $hora . ":00";
                $ocorrencias_rep->gravaOcorrencia($request->get('sala'), 'consulta', $data, $hora . ":00", null, $transacao);
            }

            // Dispatch Webhook
            \App\Helpers\WebhookHelper::dispatch('appointment.created', $params);

            $salas_rep = new SalasRepository();
            $sala = $salas_rep->getSala($request->get('sala'));

            $params = array();
            $params['medico'] = Auth::user()->name . " " . Auth::user()->sobrenome;
            $params['sala'] = $sala->nome . '-' . $sala->numero;
            $params['horarios'] = $horarios;
            $params['data'] = $data->format('d/m/Y');
            $params['credito_selecionado'] = $credito_selecionado;
            $params['valor_total'] = $valor_total;

            $teste_log['EMAIL'] = $params;
            #return view('emails.confirmacao_agendamento', $params);
            try {
                \Mail::to(Auth::user()->email)->queue(new \App\Mail\ConfirmacaoAgendamento($params));
            } catch (\Exception $e) {
                \Log::error("Failed to send confirmation email: " . $e->getMessage());
            }
            Log::debug($teste_log);
            return response()->json([
                'status' => true,
                'message' => 'Reserva feita com sucesso'
            ]);
        } else {
            Log::debug($teste_log);
            return response()->json([
                'status' => false,
                'message' => $pagamento['mensagem']
            ]);
        }
    }

    public function contaMedico(Request $request)
    {

        $agora = Carbon::now();

        $salas_rep = new SalasOcorrenciasRepository();
        $reservas = $salas_rep->getMinhasReservas(Auth::user()->id);
        $proximas = $salas_rep->getProximasReservas(Auth::user()->id, $agora);
        $anteriores = $salas_rep->getAnterioresReservas(Auth::user()->id, $agora);

        $cards_rep = new UsersCardsRepository();
        $cartoes = $cards_rep->getMeusCartoes(Auth::user()->id);

        $creditos_rep = new CreditosRepository();
        $creditos = $creditos_rep->getExtrato(Auth::user()->id);

        $user = new User();
        $medico = $user->find(Auth::user()->id);

        if ($request->isMethod('post')) {
            $erro = false;
            if (!empty($request->get('senha'))) {
                if ($request->get('senha') != $request->get('resenha')) {
                   
                    Session::flash(
                        'toastr',
                        [
                            'status' => 'warning', // success | info | warning | error
                            'message' => 'Senhas não conferem'
                        ]
                    );
                } else {
                    $medico->password = bcrypt($request->get('senha'));
                }
            }
            if (!$erro) {
                $medico->name = $request->get('name');
                $medico->sobrenome = $request->get('sobrenome');
                $medico->telefone = $request->get('telefone');
                $medico->cpf = $request->get('cpf');
                if ($medico->save()) {
                    Session::flash(
                        'toastr',
                        [
                            'status' => 'success', // success | info | warning | error
                            'message' => 'Dados alterados com sucesso'
                        ]
                    );
                } else {
                    Session::flash(
                        'toastr',
                        [
                            'status' => 'warning', // success | info | warning | error
                            'message' => 'Erro ao atualizar dados'
                        ]
                    );
                }
                $medico = $user->find(Auth::user()->id);
            }
        }

        return view('public.conta_medico', [
            'reservas' => $reservas,
            'proximas' => $proximas,
            'anteriores' => $anteriores,
            'cartoes' => $cartoes,
            'creditos' => $creditos,
            'user' => $medico
        ]);
    }

    public function cancelarReserva(Request $request)
    {
        $ocorrencias_rep = new SalasOcorrenciasRepository();
        $reserva = $ocorrencias_rep->getParaCancelar($request->get('reserva'));

        if (!$reserva) {
            return redirect()->route('minha_conta');
        }

        if (isset($reserva->status) && strtolower($reserva->status) == 'cancelado') {
            Session::flash('toastr', [
                'status' => 'info',
                'message' => 'Reserva já foi cancelada.'
            ]);

            return redirect()->route('minha_conta');
        }

        DB::transaction(function () use ($reserva, $request, $ocorrencias_rep) {
            $ocorrencias_rep->cancela($request->get('reserva'));

            $creditos_rep = new CreditosRepository();
            $creditos_rep->grava(
                $reserva->user_id,
                $reserva->valor_periodo,
                'credito',
                null
            );

            // Send Cancellation Email
            $medico = User::find($reserva->user_id);
            $params = [
                'medico' => $medico->name . " " . $medico->sobrenome,
                'sala' => $reserva->nome,
                'data' => Carbon::parse($reserva->data)->format('d/m/Y'),
                'horarios' => [$reserva->hora]
            ];

            try {
                \Mail::to($medico->email)->queue(new \App\Mail\CancelamentoAgendamento($params));
            } catch (\Exception $e) {
                \Log::error("Failed to send cancellation email: " . $e->getMessage());
            }

            // Dispatch Webhook
            \App\Helpers\WebhookHelper::dispatch('appointment.canceled', $params);
        });

        Session::flash('toastr', [
            'status' => 'success',
            'message' => 'Reserva cancelada com sucesso.'
        ]);

        return redirect()->route('minha_conta');
    }

    public function cadastroNovoMedico(Request $request)
    {        if ($request->isMethod("POST")) {

            $usuarios_rep = new UsuariosRepository();

            if (!$this->isCpfValid($request->get('cpf'))) {
                Session::flash(
                    'toastr',
                    [
                        'status' => 'warning', // success | info | warning | error
                        'message' => 'CPF Inválido!'
                    ]
                );
                return redirect()->route('cadastro_novo_medico');
            }

            $user_id = $usuarios_rep->gravaMedico($request);

            if (empty($user_id)) {
                Session::flash(
                    'toastr',
                    [
                        'status' => 'warning', // success | info | warning | error
                        'message' => 'Erro ao cadastrar!'
                    ]
                );
                return redirect()->route('cadastro_novo_medico');
            }

            // Send Welcome Email
            try {
                \Mail::to($request->get('email'))->queue(new \App\Mail\BoasVindasMedico([
                    'nome' => $request->get('name'),
                    'email' => $request->get('email')
                ]));
            } catch (\Exception $e) {
                \Log::error("Failed to send welcome email: " . $e->getMessage());
            }

            // Dispatch Webhook
            \App\Helpers\WebhookHelper::dispatch('user.registered', [
                'id' => $user_id,
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'telefone' => $request->get('telefone')
            ]);

            Session::flash(
                'toastr',
                [
                    'status' => 'success', // success | info | warning | error
                    'message' => 'Gravado com sucesso!'
                ]
            );
            //return redirect()->route('usuario.index');
            return redirect()->route('login');
        }

        return view('public.cadastro_novo_medico');
    }
}
