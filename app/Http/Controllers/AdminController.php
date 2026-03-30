<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;
use Mail;
use Log;
use App\Mail\ConfirmacaoAgendamento;
use App\Mail\CancelamentoAgendamento;

use App\PagSeguro;
use App\UsersCards;
use App\Salas;
use App\User;

use App\Repositories\UsuariosRepository;
use App\Repositories\SalasRepository;
use App\Repositories\SalasOcorrenciasRepository;
use App\Repositories\TransacoesRepository;
use App\Repositories\CreditosRepository;
use App\Repositories\UsersCardsRepository;


class AdminController extends Controller
{


    /**
     * Dashboard admin
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $ocorrencias_rep = new SalasOcorrenciasRepository();
        $salas_rep = new SalasRepository();
        $usuarios_rep = new UsuariosRepository();
        $transacoes_rep = new TransacoesRepository();

        \Log::info('Admin Dashboard Filter Request', ['inicio' => $request->get('inicio'), 'final' => $request->get('final')]);

        $naoExibir = false;
        if (!empty($request->get('inicio')) && !empty($request->get('final'))) {
            $periodo_inicio = Carbon::createFromFormat('d/m/Y', $request->get('inicio'))->startOfDay();
            if ($request->get('final') == date('d/m/Y')) {
                $naoExibir = true;
                $periodo_fim = Carbon::tomorrow();
            } else {
                $periodo_fim = Carbon::createFromFormat('d/m/Y', $request->get('final'))->endOfDay();

            }
        } else {
            $naoExibir = true;

            $data_default = Carbon::today();
            // $periodo_inicio = Carbon::create($data_default->year, $data_default->month, 1);
            $periodo_inicio = Carbon::today(); // Já vem como 00:00:00
            $periodo_fim = Carbon::tomorrow(); // Seta para 23:59:59
        }

        \Log::info('Calculated Period', ['start' => $periodo_inicio->toDateTimeString(), 'end' => $periodo_fim->toDateTimeString()]);


        $total_salas = $salas_rep->getTotalSalas();
        $total_horarios_agendados = $ocorrencias_rep->getHorariosAgendados($periodo_inicio, $periodo_fim);
        $dias = $periodo_inicio->diffInDays($periodo_fim->copy()->addSecond()) ?: 1;
        $total_horarios_livres = $ocorrencias_rep->getHorariosLivre($total_salas, $total_horarios_agendados, $dias);
        $medicos_agendados = $ocorrencias_rep->getMedicosAgendados($periodo_inicio, $periodo_fim);
        $presencial = $transacoes_rep->faturamentoPresencial($periodo_inicio, $periodo_fim);
        $online = $transacoes_rep->faturamentoOnLine($periodo_inicio, $periodo_fim);

        $top_salas = $ocorrencias_rep->getTop10Salas($periodo_inicio, $periodo_fim);
        $top_medicos = $ocorrencias_rep->getTop10Medicos($periodo_inicio, $periodo_fim);

        if ($naoExibir == true) {
            return view('admin.index', [
                'total_salas' => $total_salas,
                'total_horarios_livres' => $total_horarios_livres,
                'total_horarios_agendados' => $total_horarios_agendados,
                'medicos_agendados' => $medicos_agendados,
                'top_salas' => $top_salas,
                'top_medicos' => $top_medicos,
                'filtro_inicio' => $periodo_inicio,
                'filtro_fim' => '',
                'presencial' => $presencial,
                'online' => $online
            ]);
        } else {
            return view('admin.index', [
                'total_salas' => $total_salas,
                'total_horarios_livres' => $total_horarios_livres,
                'total_horarios_agendados' => $total_horarios_agendados,
                'medicos_agendados' => $medicos_agendados,
                'top_salas' => $top_salas,
                'top_medicos' => $top_medicos,
                'filtro_inicio' => $periodo_inicio,
                'filtro_fim' => $periodo_fim,
                'presencial' => $presencial,
                'online' => $online
            ]);
        }
    }

    public function financeiro(Request $request)
    {
        $transacoes_rep = new TransacoesRepository();
        return view('admin.financeiro', ['salas' => $transacoes_rep->financeiroSalas()]);
    }

    public function usoSala(Request $request)
    {
        $ocorrencias_rep = new SalasOcorrenciasRepository();
        $salas_rep = new SalasRepository();

        $filtro_salas = null;
        if (!empty($request->get('sala')))
            $filtro_salas = $request->get('sala');

        $filtro_inicio = Carbon::now();
        if (!empty($request->get('inicio')))
            $filtro_inicio = Carbon::createFromFormat('d/m/Y', $request->get('inicio'));

        $filtro_fim = Carbon::now();
        if (!empty($request->get('fim')))
            $filtro_fim = Carbon::createFromFormat('d/m/Y', $request->get('fim'));

        $busca = $ocorrencias_rep->usoPorSala($filtro_salas, $filtro_inicio, $filtro_fim);

        return view('admin.uso_sala', [
            'salas' => $busca['reservas'],
            'total_salas' => count($busca['total_salas']),
            'qtd_bloqueio' => $busca['qtd_bloqueio'],
            'qtd_agendamento' => $busca['qtd_agendamento'],
            'filtro_salas' => $filtro_salas,
            'filtro_inicio' => $filtro_inicio,
            'filtro_fim' => $filtro_fim,
            'salas_disponiveis' => $salas_rep->getTodas()
        ]);
    }

    public function medicosCadastrados(Request $request)
    {
        $transacoes_rep = new TransacoesRepository();
        $usuarios_rep = new UsuariosRepository();

        $filtro_medico = null;
        if (!empty($request->get('medico')))
            $filtro_medico = $request->get('medico');

        $medicos = $transacoes_rep->financeiroMedicos($filtro_medico);
        $totais = $usuarios_rep->totaisMedicos();

        return view('admin.medicos_cadastrados', [
            'medicos' => $medicos,
            'totais' => $totais,
            'filtro_medico' => $filtro_medico
        ]);
    }

    public function faturamento(Request $request)
    {
        $transacoes_rep = new TransacoesRepository();
        $salas_rep = new SalasRepository();

        $filtro_salas = null;
        if (!empty($request->get('sala')))
            $filtro_salas = $request->get('sala');

        $filtro_inicio = Carbon::now();
        if (!empty($request->get('inicio')))
            $filtro_inicio = Carbon::createFromFormat('d/m/Y', $request->get('inicio'));

        $filtro_fim = Carbon::now();
        if (!empty($request->get('fim'))) {
            $filtro_fim = Carbon::createFromFormat('d/m/Y', $request->get('fim'))->endOfDay();
        } else {
            $filtro_fim = \Carbon\Carbon::tomorrow();
        }

        $fat = $transacoes_rep->faturamento($filtro_salas, $filtro_inicio, $filtro_fim);

        if ($filtro_fim == \Carbon\Carbon::tomorrow()) {

            return view('admin.faturamento', [
                'faturamento' => $fat['faturamento'],
                'total_faturado' => $fat['total_faturado'],
                'total_medicos' => $fat['total_medicos'],
                'filtro_salas' => $filtro_salas,
                'filtro_inicio' => $filtro_inicio,
                'filtro_fim' => '',
                'salas_disponiveis' => $salas_rep->getTodas()
            ]);
        } else {
            return view('admin.faturamento', [
                'faturamento' => $fat['faturamento'],
                'total_faturado' => $fat['total_faturado'],
                'total_medicos' => $fat['total_medicos'],
                'filtro_salas' => $filtro_salas,
                'filtro_inicio' => $filtro_inicio,
                'filtro_fim' => $filtro_fim,
                'salas_disponiveis' => $salas_rep->getTodas()
            ]);
        }
    }

    public function agenda(Request $request)
    {
        $data = Carbon::now();
        $ocorrencias_rep = new SalasOcorrenciasRepository();
        $salas = $ocorrencias_rep->usoSalas($data);

        return view('admin.agenda', [
            'horarios' => $ocorrencias_rep->getHorariosFuncionamento(),
            'salas' => $salas,
            'data' => $data
        ]);
    }

    public function cadastraOcorrencia(Request $request)
    {
        $ocorrencias_rep = new SalasOcorrenciasRepository();
        $dt_ini = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('datepickerIni') . " 00:00:00");
        $dt_fim = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('datepickerFim') . " 00:00:00");
        $dias = $dt_ini->diffInDays($dt_fim);

        $existentes = [];
        $novas_ocorrencias = [];
        $final_dt = clone $dt_ini;

        for ($i = 0; $i <= $dias; $i++) {
            $data_str = $final_dt->format('Y-m-d');
            foreach ($request->get('horario') as $key => $h) {
                if ($h == 1) {
                    foreach ($request->get('salas') as $sala) {
                        if ($ocorrencias_rep->verificaExisteBloqueio($sala, $data_str, $key)) {
                            $existentes[] = $final_dt->format('d/m/Y') . '-' . $key;
                        } else {
                            $novas_ocorrencias[] = [
                                'sala_id' => $sala,
                                'user_id' => \Auth::user()->id,
                                'data' => $data_str,
                                'hora' => $key,
                                'tipo' => 'BLOQUEIO',
                                'comentario' => $request->get('motivo'),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
            }
            $final_dt->addDay();
        }

        if (!empty($novas_ocorrencias)) {
            \DB::table('salas_ocorrencias')->insert($novas_ocorrencias);
        }

        $msg = "";
        if (!empty($existentes)) {
            $msg = "Os horários a seguir foram desconsiderados, bloqueios já efetuados: " . implode(', ', array_unique($existentes));
        }

        Session::flash('toastr', [
            'status' => 'success',
            'message' => 'Bloqueio realizado com sucesso. ' . $msg
        ]);

        return redirect()->route('admin.agendamento');
    }

    public function agendamento(Request $request)
    {

        $salas_rep = new SalasRepository();
        $usuarios_rep = new UsuariosRepository();
        $ocorrencias_rep = new SalasOcorrenciasRepository();
        $cartoes_rep = new UsersCardsRepository();
        $creditos_rep = new CreditosRepository();

        $medico = null;
        $sala = null;
        $data_inicial = Carbon::today();
        $data_final = Carbon::today();
        $horarios_sel = [];
        $msg_ocorrencias = [];
        $cartoes = array();
        $pagamento = false;
        $valor = 0;

        if ($request->isMethod('post')) {

            if (!empty($request->get('medico'))) {
                $medico = $request->get('medico');
                $cartoes = $cartoes_rep->getMeusCartoes($request->get('medico'));
            } else {
                $msg_ocorrencias['medico'] = "Selecione um médico";
            }

            if (!empty($request->get('sala'))) {
                $sala = $salas_rep->getSala($request->get('sala'));
            } else {
                $msg_ocorrencias['sala'] = "Selecione uma sala";
            }

            if (!empty($request->get('data_inicial'))) {
                $data_inicial = Carbon::createFromFormat('d/m/Y', $request->get('data_inicial'));
            } else {
                $msg_ocorrencias['data_inicial'] = "Selecione uma data de início";
            }

            if (!empty($request->get('data_final'))) {
                $data_final = Carbon::createFromFormat('d/m/Y', $request->get('data_final'));
            } else {
                $msg_ocorrencias['data_final'] = "Selecione uma data de fim";
            }

            if (!empty($request->get('horario'))) {
                foreach ($request->get('horario') as $hora => $sel) {
                    if ($sel == 1)
                        $horarios_sel[] = $hora;
                }
            }

            if (empty($horarios_sel)) {
                $msg_ocorrencias['horario'] = "Selecione ao menos um horário para reserva";
            }

            if (!empty($horarios_sel) && !empty($data_inicial) && !empty($data_final) && !empty($sala)) {
                $ocorrencias = $ocorrencias_rep->buscaOcorrencias($sala, $horarios_sel, $data_inicial, $data_final);
                if (!$ocorrencias) {
                    $dias = $data_inicial->diffInDays($data_final) + 1;
                    $valor = $sala->valor_periodo * ($dias * count($horarios_sel));
                    $pagamento = true;
                } else {
                    $msg_ocorrencias['ocorrencia'] = "Agendamento não permitido!<br><br>";
                    foreach ($ocorrencias as $o) {
                        $msg_ocorrencias['ocorrencia'] .= Carbon::createFromFormat('Y-m-d H:i:s', $o->data . " " . $o->hora)->format('d/m/Y H:i') . "-" . $o->tipo . "<br>";
                    }
                }
            }
        }

        return view('admin.agendamento', [
            'salas' => $salas_rep->getSalas(),
            'medicos' => $usuarios_rep->getAll('medico'),
            'horarios' => $ocorrencias_rep->getHorariosFuncionamento(),
            'horarios_sel' => $horarios_sel,
            'data_inicial' => $data_inicial,
            'data_final' => $data_final,
            'medico' => $medico,
            'medico_selecionado' => $request->get('medico'),
            'sala' => $sala,
            'sala_selecionada' => $request->get('sala'),
            'cartoes' => $cartoes,
            'pagamento' => $pagamento,
            'msg_ocorrencias' => $msg_ocorrencias,
            'credito_medico' => $creditos_rep->getExtrato($request->get('medico')),
            'valor' => $valor
        ]);
    }

    public function vendaCredito(Request $request)
    {

        $usuarios_rep = new UsuariosRepository();
        $cartoes_rep = new UsersCardsRepository();

        $cartoes = array();
        $medico = null;
        if ($request->isMethod('post')) {
            if (!empty($request->get('medico'))) {
                $cartoes = $cartoes_rep->getMeusCartoes($request->get('medico'));
                $medico = $request->get('medico');
            }
        }

        return view('admin.venda_credito', [
            'medicos' => $usuarios_rep->getAll('medico'),
            'cartoes' => $cartoes,
            'medico' => $medico
        ]);
    }

    public function vendaCreditos(Request $request)
    {

        $creditos_rep = new CreditosRepository();
        $usuarios_rep = new UsuariosRepository();

        $filtro_medico = null;
        if (!empty($request->get('medico')))
            $filtro_medico = $request->get('medico');

        $filtro_inicio = Carbon::now();
        if (!empty($request->get('inicio')))
            $filtro_inicio = Carbon::createFromFormat('d/m/Y', $request->get('inicio'));

        $filtro_fim = Carbon::now();

        if (!empty($request->get('fim'))) {
            $filtro_fim = Carbon::createFromFormat('d/m/Y', $request->get('fim'))->endOfDay();
        } else {
            $filtro_fim = \Carbon\Carbon::tomorrow();
        }

        $cred = $creditos_rep->vendaCreditos($filtro_medico, $filtro_inicio, $filtro_fim);

        if ($filtro_fim == \Carbon\Carbon::tomorrow()) {

            return view('admin.relat_venda_creditos', [
                'creditos' => $cred['creditos'],
                'valor_cobrado' => $cred['valor_cobrado'],
                'valor_creditado' => $cred['valor_creditado'],
                'filtro_medico' => $filtro_medico,
                'filtro_inicio' => $filtro_inicio,
                'filtro_fim' => '',
                'medicos' => $usuarios_rep->getAll('medico')
            ]);
        } else {
            return view('admin.relat_venda_creditos', [
                'creditos' => $cred['creditos'],
                'valor_cobrado' => $cred['valor_cobrado'],
                'valor_creditado' => $cred['valor_creditado'],
                'filtro_medico' => $filtro_medico,
                'filtro_inicio' => $filtro_inicio,
                'filtro_fim' => $filtro_fim,
                'medicos' => $usuarios_rep->getAll('medico')
            ]);
        }
    }

    public function saldoCreditos(Request $request)
    {

        $creditos_rep = new CreditosRepository();
        $usuarios_rep = new UsuariosRepository();

        $filtro_medicos = null;
        if (!empty($request->get('medico')))
            $filtro_medicos = $request->get('medico');

        $filtro_inicio = Carbon::now();
        if (!empty($request->get('inicio')))
            $filtro_inicio = Carbon::createFromFormat('d/m/Y', $request->get('inicio'));

        $filtro_fim = Carbon::now();
        if (!empty($request->get('fim'))) {
            $filtro_fim = Carbon::createFromFormat('d/m/Y', $request->get('fim'));
        } else {
            $filtro_fim = \Carbon\Carbon::tomorrow();
        }

        $cred = $creditos_rep->saldoCreditos($filtro_medicos, $filtro_inicio, $filtro_fim);

        if ($filtro_fim == \Carbon\Carbon::tomorrow()) {
            return view('admin.saldo_creditos', [
                'creditos' => $cred['creditos'],
                'valor_credito' => $cred['valor_credito'],
                'valor_debito' => $cred['valor_debito'],
                'filtro_medicos' => $filtro_medicos,
                'filtro_inicio' => $filtro_inicio,
                'filtro_fim' => '',
                'medicos' => $usuarios_rep->getAll('medico')
            ]);
        } else {
            return view('admin.saldo_creditos', [
                'creditos' => $cred['creditos'],
                'valor_credito' => $cred['valor_credito'],
                'valor_debito' => $cred['valor_debito'],
                'filtro_medicos' => $filtro_medicos,
                'filtro_inicio' => $filtro_inicio,
                'filtro_fim' => $filtro_fim,
                'medicos' => $usuarios_rep->getAll('medico')
            ]);
        }
    }

    public function checkoutVendaCredito(Request $request)
    {
        try {
            $valor_credito = str_replace(',', '.', str_replace('.', '', $request->get('valor_credito')));
            $valor_cobranca = str_replace(',', '.', str_replace('.', '', $request->get('valor_cobranca')));

            $creditos_rep = new CreditosRepository();
            $transacoes_rep = new TransacoesRepository();

            // Pagamento presencial com maquina só credica
            if (!empty($request->get('presencial'))) {
                /* CRIA TRANSACAO E CREDITO */
                $transacao = $transacoes_rep->createPresencial($valor_cobranca, null, $request->get('medico'));
                $credito = $creditos_rep->grava($request->get('medico'), $valor_credito, 'credito', $transacao);
                return response()->json([
                    'status' => true,
                    'message' => 'Creditado com sucesso'
                ]);
            }

            $cartao = array();
            if (!empty($request->get('cartao_precadastrado'))) {
                $cartao = UsersCards::find($request->get('cartao_precadastrado'));
            }

            $pagamento = ['status' => true, 'retorno' => null];
            
            if ($valor_cobranca > 0) {
                $pagseguro = new Pagseguro();
                $pagamento = $pagseguro->charge($request, $valor_cobranca, $cartao, $request->get('medico'));
            }

            if ($pagamento['status'] == true) {

                /* CRIA TRANSACAO E CREDITO */
                $transacao = $transacoes_rep->createOnline($pagamento['retorno'], null, $request->get('medico'));
                $credito = $creditos_rep->grava($request->get('medico'), $valor_credito, 'credito', $transacao);

                if (!empty($request->get('gravar_cartao'))) {
                    $cards_rep = new UsersCardsRepository();
                    $cards_rep->create($pagamento['retorno']->payment_method->card, $request->get('medico'));
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Creditado com sucesso'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $pagamento['mensagem']
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("checkoutVendaCredito Error: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erro interno ao processar pagamento: ' . $e->getMessage()
            ]);
        }
    }

    public function checkoutAgendamento(Request $request)
    {

        $creditos_rep = new CreditosRepository();
        $transacoes_rep = new TransacoesRepository();
        $ocorrencias_rep = new SalasOcorrenciasRepository();
        $salas_rep = new SalasRepository();

        try {
            $credito_medico = $creditos_rep->getExtrato($request->get('medico'));

            /* Valor do crédito que será usado */
            $valor_credito = str_replace(',', '.', str_replace('.', '', $request->get('valor_credito')));
            /* Restante que será cobrado */
            $valor_cobranca = str_replace(',', '.', str_replace('.', '', $request->get('valor_cobranca')));

            /** Intervalo de datas */
            $data_inicial = Carbon::createFromFormat('d/m/Y', $request->get('data_inicial'));
            $data_final = Carbon::createFromFormat('d/m/Y', $request->get('data_final'));
            $horarios_sel = array();
            foreach ($request->get('horario') as $hora => $sel) {
                if ($sel == 1) {
                    $horarios_sel[] = $hora;
                }
            }

            /** Re-verifica se horários estão livres */
            $ocorrencias = $ocorrencias_rep->buscaOcorrencias($salas_rep->getSala($request->get('sala')), $horarios_sel, $data_inicial, $data_final);
            if ($ocorrencias) {
                return response()->json([
                    'status' => false,
                    'message' => 'Horário indisponível. Tente novamente.'
                ]);
            }

            /** Fará a cobrança por maquininha */
            if (!empty($request->get('presencial'))) {
                /* CRIA TRANSACAO E CREDITO */
                $transacao = null;
                if ($valor_cobranca > 0)
                    $transacao = $transacoes_rep->createPresencial($valor_cobranca, $request->get('sala'), $request->get('medico'));

                if ($valor_credito >= 0 && $valor_credito <= $credito_medico['saldo'])
                    $credito = $creditos_rep->grava($request->get('medico'), $valor_credito, 'debito', null);
                else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Saldo de créditos insuficiente.'
                    ]);
                }

                $ocorrencias = $ocorrencias_rep->gravaIntervalo($data_inicial, $data_final, $horarios_sel, $request->get('medico'), $request->get('sala'), $transacao);

                /**
                 * Envio de email confirmação
                 */
                $salas_rep = new SalasRepository();
                $sala = $salas_rep->getSala($request->get('sala'));

                $medico = User::find($request->get('medico'));

                $params = array();
                $params['medico'] = $medico->name . " " . $medico->sobrenome;
                $params['email'] = $medico->email;
                $params['sala'] = $sala->nome . '-' . $sala->numero;
                $params['horarios'] = $horarios_sel;
                $params['data'] = $data_inicial->format('d/m/Y') . " até " . $data_final->format('d/m/Y');
                $params['credito_selecionado'] = $valor_credito;
                $params['valor_total'] = $valor_cobranca;

                try {
                    \Mail::to($medico->email)->queue(new \App\Mail\ConfirmacaoAgendamento($params));
                } catch (\Exception $e) {
                    \Log::error("Failed to send admin booking confirmation email: " . $e->getMessage());
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Reservado com sucesso'
                ]);
            } else {
                $cartao = array();
                if (!empty($request->get('cartao_precadastrado'))) {
                    $cartao = UsersCards::find($request->get('cartao_precadastrado'));
                }

                $pagamento = ['status' => true, 'retorno' => null];
                
                if ($valor_cobranca > 0) {
                    $pagseguro = new Pagseguro();
                    $pagamento = $pagseguro->charge($request, $valor_cobranca, $cartao, $request->get('medico'));
                }

                if ($pagamento['status'] == true) {

                    /* CRIA TRANSACAO E CREDITO */
                    $transacao = null;
                    if ($pagamento['retorno']) {
                        $transacao = $transacoes_rep->createOnline($pagamento['retorno'], null, $request->get('medico'));
                    }
                    
                    if ($valor_credito > 0)
                        $credito = $creditos_rep->grava($request->get('medico'), $valor_credito, 'debito', $transacao);

                    if (!empty($request->get('gravar_cartao')) && $pagamento['retorno']) {
                        $cards_rep = new UsersCardsRepository();
                        $cards_rep->create($pagamento['retorno']->payment_method->card, $request->get('medico'));
                    }

                    $ocorrencias = $ocorrencias_rep->gravaIntervalo($data_inicial, $data_final, $horarios_sel, $request->get('medico'), $request->get('sala'), $transacao);

                    /**
                     * Envio de email confirmação
                     */
                    $salas_rep = new SalasRepository();
                    $sala = $salas_rep->getSala($request->get('sala'));

                    $medico = User::find($request->get('medico'));

                    $params = array();
                    $params['medico'] = $medico->name . " " . $medico->sobrenome;
                    $params['sala'] = $sala->nome . '-' . $sala->numero;
                    $params['horarios'] = $horarios_sel;
                    $params['data'] = $data_inicial->format('d/m/Y') . " até " . $data_final->format('d/m/Y');

                    return response()->json([
                        'status' => true,
                        'message' => 'Creditado com sucesso'
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => $pagamento['mensagem']
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error("checkoutAgendamento Error: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erro interno ao processar agendamento: ' . $e->getMessage()
            ]);
        }
    }
}
