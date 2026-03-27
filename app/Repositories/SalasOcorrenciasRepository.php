<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;

use App\SalasOcorrencias;

class SalasOcorrenciasRepository
{

    public $horarios_funcionamento = array(
        "08:00","08:30","09:00","09:30","10:00","10:30",
        "11:00","11:30","12:00","12:30","13:00","13:30",
        "14:00","14:30","15:00","15:30","16:00","16:30",
        "17:00","17:30","18:00","18:30","19:00","19:30",
        "20:00","20:30","21:00","21:30"
    );

    public function getHorariosPeriodo() {
        $retorno = [];
        $horarios = $this->horarios_funcionamento;
        foreach ($horarios as $k => $h) {
            if (!empty($horarios[$k + 1])) {
                $retorno[] = $h."-".$horarios[$k + 1];
            }
        }
        return $retorno;
    }

    private $model;
    public function __construct() {
        $this->model = new SalasOcorrencias();
    }

    public function getHorariosFuncionamento() {
        return $this->horarios_funcionamento;
    }

    public function gravaIntervalo($data_inicial,$data_final,$horarios_sel,$medico,$sala,$transacao) {
        $dias = $data_inicial->diffInDays($data_final);
        $records = [];
        for ($i = 0; $i <= $dias; $i++) {
            foreach ($horarios_sel as $key => $h) {
                $records[] = [
                    'sala_id' => $sala,
                    'user_id' => $medico,
                    'data' => $data_inicial->format('Y-m-d'),
                    'hora' => $h,
                    'tipo' => 'consulta',
                    'comentario' => null,
                    'transacao_id' => $transacao,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $data_inicial->addDay();
        }
        if (!empty($records)) {
            DB::table('salas_ocorrencias')->insert($records);
        }
    }

    public function gravaOcorrencia($sala,$tipo,$data,$hora,$comentario = null,$transacao = null) {
        $nova_ocorrencia = new SalasOcorrencias();
        $nova_ocorrencia->sala_id = $sala;
        $nova_ocorrencia->user_id = Auth::user()->id;
        $nova_ocorrencia->data = $data;
        $nova_ocorrencia->hora = $hora;
        $nova_ocorrencia->tipo = $tipo;
        $nova_ocorrencia->comentario = $comentario;
        $nova_ocorrencia->transacao_id = $transacao;
        $nova_ocorrencia->save();
    }

    public function getHorariosLivre($tot_salas,$tot_ocorrencias,$dias) {
        $total_horarios = (($tot_salas * count($this->horarios_funcionamento)) * $dias) - $tot_ocorrencias;
        return $total_horarios;
    }

    public function getHorariosAgendados($inicio,$fim) {
        $total_ocorrencias = DB::table('salas_ocorrencias')
            ->select('salas_ocorrencias.*')
            ->where([
                ['tipo', '=','consulta'],
                ['data','>=',$inicio],
                ['data','<=',$fim]
                ])
            ->count();
        return $total_ocorrencias;
    }

    public function getMedicosAgendados($inicio,$fim) {
        $total_ocorrencias = DB::table('salas_ocorrencias')
            ->select('salas_ocorrencias.user_id')
            ->where([
                ['data','>=',$inicio],
                ['data','<=',$fim],
                ['tipo','consulta']
                ])
            ->groupBy('user_id')
            ->get();
        return count($total_ocorrencias);
    }

    public function getMinhasReservas($user_id) {
        $reservas = DB::table('salas_ocorrencias')
        ->select('salas_ocorrencias.*','salas.nome')
        ->join('salas', 'salas.id', '=', 'salas_ocorrencias.sala_id')
        ->where([
            ['user_id',$user_id],
            ['tipo','consulta']
            ])
        ->orderBy('data')
        ->orderBy('hora','desc')
        ->get();
        return $reservas;
    }

    public function getProximasReservas($user_id,$data) {
        $reservas = DB::table('salas_ocorrencias')
        ->select('salas_ocorrencias.*','salas.nome')
        ->join('salas', 'salas.id', '=', 'salas_ocorrencias.sala_id')
        ->where([
            ['user_id',$user_id],
            ['data','>',$data->format('Y-m-d')],
            ['tipo','consulta']
            ])
        ->orWhere([
            ['user_id',$user_id],
            ['data','=',$data->format('Y-m-d')],
            ['hora','>=',$data->format('H:i')],
            ['tipo','consulta']
            ])
        ->orderBy('data')
        ->orderBy('hora','asc')
        ->get();

        $horario_maximo_cancelamento = Carbon::today();
        $horario_maximo_cancelamento->setHour(22);

        $agora = Carbon::now();
        foreach ($reservas as $k => $r) {
            $data_reserva = Carbon::createFromFormat('Y-m-d H:i:s',$r->data." ".$r->hora);
            $diff = $data_reserva->diff($agora);
            /*if ($agora <= $horario_maximo_cancelamento && $diff->days >= 1) {
                $reservas[$k]->pode_cancelar = true;
            } elseif ($agora > $horario_maximo_cancelamento && $diff->days >= 2) {
                $reservas[$k]->pode_cancelar = true;
            } else {
                $reservas[$k]->pode_cancelar = false;
	    }*/
            if ($diff->days >= 1) {
                $reservas[$k]->pode_cancelar = true;
            } elseif ($agora <= $horario_maximo_cancelamento && $diff->h >= 10) {
                $reservas[$k]->pode_cancelar = true;
            } else {
                $reservas[$k]->pode_cancelar = false;
	    }
        }
        return $reservas;
    }

    public function getAnterioresReservas($user_id,$data) {
        $reservas = DB::table('salas_ocorrencias')
        ->select('salas_ocorrencias.*','salas.nome')
        ->join('salas', 'salas.id', '=', 'salas_ocorrencias.sala_id')
        ->where([
            ['user_id',$user_id],
            ['data','<',$data->format('Y-m-d')],
            ['tipo','consulta']
            ])
        ->orWhere([
            ['user_id',$user_id],
            ['data','=',$data->format('Y-m-d')],
            ['hora','<=',$data->format('H:i')],
            ['tipo','consulta']
            ])
        ->orderBy('data')
        ->orderBy('hora','desc')
        ->get();
        return $reservas;
    }

    public function getParaCancelar($id) {
        $reserva = DB::table('salas_ocorrencias')
        ->select('salas.valor_periodo','salas_ocorrencias.user_id', 'salas.nome', 'salas_ocorrencias.data', 'salas_ocorrencias.hora', 'salas_ocorrencias.status')
        ->join('salas', 'salas.id', '=', 'salas_ocorrencias.sala_id')
        ->where('salas_ocorrencias.id',$id)
        ->first();
        return $reserva;
    }

    public function getOcorrencia($id) {
        return $this->model->find($id);
    }

    public function cancela($id) {
        $reserva = $this->model->find($id);
        $reserva->tipo = 'consulta_cancelada';
        $reserva->save();
        return true;
    }

    public function getTop10Salas() {
        $top10salas = DB::select("
            SELECT * FROM (
                SELECT COUNT(*) AS total, sala_id FROM salas_ocorrencias WHERE tipo = 'consulta' GROUP BY sala_id) sub
            JOIN salas ON (sub.sala_id = salas.id) ORDER BY sub.total desc limit 10
        ");
        return $top10salas;
    }

    public function getTop10Medicos() {
        $top10medicos = DB::select("
            SELECT * FROM (
                SELECT COUNT(*) AS total, user_id FROM salas_ocorrencias WHERE tipo = 'consulta' GROUP BY user_id) sub
            JOIN users ON (sub.user_id = users.id) ORDER BY sub.total desc limit 10
        ");
        return $top10medicos;
    }

    public function usoPorSala($sala,$inicio,$fim) {

        $where_sala = null;
        if (!empty($sala))
            $where_sala = "AND so.sala_id = $sala";

        $where_inicio = null;
        if (!empty($inicio))
            $where_inicio = "AND so.data >= '".$inicio->format('Y-m-d')."'";

        $where_fim = null;
        if (!empty($fim))
            $where_fim = "AND so.data <= '".$fim->format('Y-m-d')."'";


        $uso_sala = DB::select("
            SELECT so.id,so.data,s.numero,s.nome,so.hora,u.name,u.sobrenome,u.telefone,t.valor,t.tipo,so.sala_id,s.valor_periodo, so.tipo as so_tipo FROM salas_ocorrencias so
            JOIN users u ON so.user_id = u.id
            JOIN salas s ON so.sala_id = s.id
            LEFT JOIN transacoes t ON so.transacao_id = t.id
            WHERE (so.tipo = 'consulta' OR so.tipo = 'BLOQUEIO')
            {$where_sala}
            {$where_inicio}
            {$where_fim}
            ORDER BY so.data,so.hora
        ");

        $salas = DB::select("
            SELECT distinct(so.sala_id) FROM salas_ocorrencias so
            WHERE so.tipo = 'consulta'
            {$where_sala}
            {$where_inicio}
            {$where_fim}
        ");

        $qtd_bloqueio = DB::select("
            SELECT 1 FROM salas_ocorrencias so
            JOIN users u ON so.user_id = u.id
            JOIN salas s ON so.sala_id = s.id
            LEFT JOIN transacoes t ON so.transacao_id = t.id
            WHERE so.tipo = 'BLOQUEIO'
            {$where_sala}
            {$where_inicio}
            {$where_fim}
        ");

        $qtd_agendamento = DB::select("
            SELECT 1 FROM salas_ocorrencias so
            JOIN users u ON so.user_id = u.id
            JOIN salas s ON so.sala_id = s.id
            LEFT JOIN transacoes t ON so.transacao_id = t.id
            WHERE so.tipo = 'consulta'
            {$where_sala}
            {$where_inicio}
            {$where_fim}
        ");

        return array(
            'total_salas' => $salas,
            'reservas' => $uso_sala,
            'qtd_bloqueio' => count($qtd_bloqueio),
            'qtd_agendamento' =>  count($qtd_agendamento)
        );
    }

    public function usoSalas($data) {
        $salas = DB::table('salas')->get();
        foreach ($salas as $k => $s) {
            $reservas = DB::table('salas_ocorrencias')
            ->select('*')
            ->join('salas', 'salas.id', '=', 'salas_ocorrencias.sala_id')
            ->join('users', 'users.id', '=', 'salas_ocorrencias.user_id')
            ->where([
                ['data',$data->format('Y-m-d')],
                ['tipo','!=','consulta_cancelada']
                ])
            ->orderBy('hora','asc')
            ->get();

            $salas[$k]->reservas = array();
            foreach($reservas as $r) {
                $salas[$k]->reservas[$r->hora] = array(
                    'usuario' => $r->name." ".$r->sobrenome,
                    'tipo' => $r->tipo
                );
            }
        }
        return $salas;
    }

    public function getOcorrencias($data,$sala_id) {
        $ocorrencias = DB::table('salas_ocorrencias')
            ->select('salas_ocorrencias.*')
            ->where([
                ['sala_id',$sala_id],
                ['data',$data],
                ['tipo','!=','consulta_cancelada']
                ])
            ->get();

        $return = array();
        foreach ($ocorrencias as $o) {
            $hora = Carbon::createFromFormat('H:i:s',$o->hora)->format('H:i');
            $return[] = $hora;
            if ($o->tipo == 'consulta' || $o->tipo == 'BLOQUEIO')
                $return[] = Carbon::createfromformat('H:i:s',$o->hora)->addMinutes(30)->format('H:i');
        }
        return $return;
    }

    public function verificaExisteBloqueio($sala, $data, $hora)
    {
        $ocorrencias = DB::table('salas_ocorrencias')
            ->select('*')
            ->where('tipo', 'BLOQUEIO')
            ->where('data', $data)
            ->where('hora', $hora)
            ->where('sala_id', $sala)
            ->count();

        return ($ocorrencias > 0)? true : false;
    }


public function buscaOcorrencias($sala,$horarios_sel,$data_inicial,$data_final) {

    $horarios_consulta = [];
    $horarios_outros = [];

    foreach ($horarios_sel as $h) {
        $obj_hora = Carbon::createfromformat('H:i',$h);

        $horarios_consulta[] = $horarios_outros[] = $obj_hora->format('H:i');
        $horarios_consulta[] = $horarios_outros[] = $obj_hora->addMinutes(30)->format('H:i');
        $horarios_consulta[] = $obj_hora->subMinutes(30)->format('H:i');
    }

    $ocorrencias = DB::table('salas_ocorrencias')
        ->select('*')
        ->where(function($query) use ($sala, $data_inicial, $data_final, $horarios_consulta) {
            $query->where('sala_id', $sala->id)
                ->where('tipo','consulta')
                ->whereBetween('data', [
                    $data_inicial->format('Y-m-d') . ' 00:00:00',
                    $data_final->format('Y-m-d') . ' 23:59:59'
                ])
                ->whereIn('hora',$horarios_consulta);
        })
        ->orWhere(function($query) use ($sala, $data_inicial, $data_final, $horarios_outros) {
            $query->where('sala_id', $sala->id)
                ->whereNotIn('tipo',['consulta','consulta_cancelada'])
                ->whereBetween('data', [
                    $data_inicial->format('Y-m-d') . ' 00:00:00',
                    $data_final->format('Y-m-d') . ' 23:59:59'
                ])
                ->whereIn('hora',$horarios_outros);
        })
        ->get();

    if (count($ocorrencias) > 0) {
        return $ocorrencias;
    }

    return false;
}


}
