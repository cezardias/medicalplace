<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;

use App\Creditos;

class CreditosRepository 
{

    private $model;

    public function __construct() {
        $this->model = new Creditos();
    }

    public function getExtrato($user) {
        $creditos = DB::table('creditos')
            ->select('creditos.*')
            ->where('user_id',$user)
            ->get();

        $saldo = 0;
        foreach($creditos as $c) {
            if ($c->tipo == 'credito') {
                $saldo += $c->valor;
            } elseif ($c->tipo == 'debito') {
                $saldo -= $c->valor;
            }
        }
        return array(
            'saldo' => $saldo,
            'lancamentos' => $creditos
        );
    }
    
    public function grava($user,$valor,$tipo,$transacao = null) {

        $this->model->transacao_id = $transacao;
        $this->model->user_id = $user;
        $this->model->valor = $valor;
        $this->model->tipo = $tipo;
        $this->model->save();

        return $this->model->id;

    }

    public function vendaCreditos($filtro_medico,$filtro_inicio,$filtro_fim) {

        $where_medico = null;
        if (!empty($filtro_medico))
            $where_medico = "AND c.user_id = $filtro_medico";
        $where_inicio = null;
        if (!empty($filtro_inicio))
            $where_inicio = "AND c.created_at >= '".$filtro_inicio->format('Y-m-d')."'";
        $where_fim = null;
        if (!empty($filtro_fim))
            $where_fim = "AND c.created_at <= '".$filtro_fim->format('Y-m-d H:i:s')."'";

        $creditos = DB::select("
            SELECT c.*,t.valor as valor_cobrado,u.name,u.sobrenome,u.cpf FROM creditos c 
            JOIN users u ON u.id = c.user_id
            JOIN transacoes t ON t.id = c.transacao_id
            where 
            c.transacao_id is not null
            {$where_medico}
            {$where_inicio}
            {$where_fim}
            ORDER BY c.created_at desc
        ");

        $valor_cobrado = 0;
        $valor_creditado = 0;
        foreach ($creditos as $c) {
            $valor_cobrado += $c->valor_cobrado;
            $valor_creditado += $c->valor;
        }

        return array(
            'creditos' => $creditos,
            'valor_cobrado' => $valor_cobrado,
            'valor_creditado' => $valor_creditado
        );
    }

    public function saldoCreditos($filtro_medico,$filtro_inicio,$filtro_fim) {

        $where_medico = null;
        if (!empty($filtro_medico))
            $where_medico = "AND user_id = $filtro_medico";
        $where_inicio = null;
        if (!empty($filtro_inicio))
            $where_inicio = "AND created_at >= '".$filtro_inicio->format('Y-m-d')."'";
        $where_fim = null;
        if (!empty($filtro_fim))
            $where_fim = "AND created_at <= '".$filtro_fim->format('Y-m-d')."'";

        $creditos = DB::select("
            select 
                sum(c.credito) as credito, 
                sum(c.debito) as debito,
                u.name,
                u.sobrenome,
                u.cpf 
            from (
                select 
                    user_id, 
                    if(tipo='credito',valor,0) as credito, 
                    if(tipo='debito',valor,0) as debito 
                from 
                    creditos
                where 1
                {$where_medico}
                {$where_inicio}
                {$where_fim}
                ) as c 
            join users u on c.user_id = u.id group by c.user_id, u.name, u.sobrenome, u.cpf
        ");

        $valor_credito = 0;
        $valor_debito = 0;
        foreach ($creditos as $c) {
            $valor_credito += $c->credito;
            $valor_debito += $c->debito;
        }

        return array(
            'creditos' => $creditos,
            'valor_credito' => $valor_credito,
            'valor_debito' => $valor_debito
        );
    }


}