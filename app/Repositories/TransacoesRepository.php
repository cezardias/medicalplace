<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;

use App\Transacoes;

class TransacoesRepository 
{
    private $model;
    
    public function __construct() {
        $this->model = new Transacoes();
    }

    public function createOnline($dados,$sala,$user) {
        $this->model->codigo_transacao = $dados->id;
        $this->model->reference = $dados->reference_id;
        $this->model->valor = $dados->amount->value / 100; // É cobrado em centavos
        $this->model->sala_id = $sala;
        $this->model->user_id = $user;
        $this->model->status = $dados->payment_response->code;
        $this->model->card = json_encode($dados->payment_method->card);
        $this->model->tipo = 'venda_online';
        $this->model->save();
        return $this->model->id;
    }

    public function createPresencial($valor,$sala,$user) {
        $this->model->valor = $valor;
        $this->model->status = 'OK';
        $this->model->sala_id = $sala;
        $this->model->user_id = $user;
        $this->model->tipo = 'presencial';
        $this->model->save();
        return $this->model->id;
    }


    public function financeiroSalas() {
        $financeiro = DB::select("
            SELECT sub.total, s.nome, si.caminho FROM 
                salas s
                LEFT JOIN (SELECT SUM(valor) AS total,sala_id FROM transacoes GROUP BY sala_id) sub ON s.id = sub.sala_id
                LEFT JOIN salas_imagens si ON s.id = si.sala_id AND si.tipo = 'capa'
            order by sub.total desc
        ");
    return $financeiro;
    }

    public function financeiroMedicos($medico = null) {

        $where = null;
        if (!empty($medico))
            $where = "and u.id = $medico ";

        $financeiro = DB::select("
        SELECT u.id,u.created_at,u.email,u.name,u.sobrenome,u.telefone,sub.valor,sub.qt,u.status,sub.ultimo_agendamento FROM users u 
        LEFT JOIN (SELECT SUM(valor) AS valor, COUNT(*) AS qt, user_id, MAX(created_at) AS ultimo_agendamento FROM transacoes t GROUP BY user_id) sub ON sub.user_id = u.id
        WHERE u.role = 'medico' $where
        ORDER BY sub.valor DESC
        ");
        return $financeiro;
    }

    public function faturamento($salas,$inicio,$fim) {

        $where_sala = null;
        if (!empty($salas))
            $where_sala = "AND s.id = $salas";
        $where_inicio = null;
        if (!empty($inicio))
            $where_inicio = "AND t.created_at >= '".$inicio->format('Y-m-d')."'";
        $where_fim = null;
        if (!empty($fim))
            $where_fim = "AND t.created_at <= '".$fim->format('Y-m-d H:i:s')."'";

        $faturamento = DB::select("
        SELECT t.valor,s.numero,s.nome AS nome_sala, u.id as user_id, u.name, u.sobrenome, t.codigo_transacao, t.status, t.created_at, t.tipo FROM transacoes t 
        JOIN users u ON u.id = t.user_id
        LEFT JOIN salas s ON s.id = t.sala_id
        where 1
        {$where_sala}
        {$where_inicio}
        {$where_fim}
        ORDER BY t.valor desc
        ");


        $tot_fat = 0;
        $medicos = array();
        foreach ($faturamento as $f) {
            $tot_fat += $f->valor;
            $medicos[$f->user_id] = 1;
        }
        
        return array(
            'faturamento' => $faturamento,
            'total_faturado' => $tot_fat,
            'total_medicos' => $medicos
        );
    }

    public function faturamentoPresencial($inicio,$fim) {
        $faturamento = DB::select("
        SELECT sum(valor) as faturamento FROM transacoes 
        WHERE created_at >= '".$inicio->format('Y-m-d H:i:s')."' 
        AND created_at <= '".$fim->format('Y-m-d H:i:s')."' 
        AND tipo = 'presencial'
        ");
        return $faturamento[0];
    }    
    public function faturamentoOnLine($inicio,$fim) {

        $faturamento = DB::select("
        SELECT sum(valor) as faturamento FROM transacoes 
        WHERE created_at >= '".$inicio->format('Y-m-d H:i:s')."' 
        AND created_at <= '".$fim->format('Y-m-d H:i:s')."' 
        AND tipo = 'venda_online'
        ");
        return $faturamento[0];
    }



}
