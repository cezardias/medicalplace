<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use App\Salas;
use App\Repositories\SalasOcorrenciasRepository;

class SalasRepository
{
    private $model;

    public function __construct() {
        $this->model = new Salas();
    }

public function grava($request, $id_sala = null) {

    $slug = \App\Helper\Funcoes::instance()->slugfy($request->get('nome')." ".$request->get('numero'));

    $sala = $this->model;

    if (!empty($id_sala)) {
        $sala = $this->model->find($id_sala);
    }

    $sala->nome = $request->get('nome');
    $sala->numero = $request->get('numero');
    $sala->descricao = $request->get('descricao');

    // Corrigir formatação do valor monetário
    $valor = $request->get('valor_periodo');

    if (!empty($valor)) {
        // remove separador de milhar
        $valor = str_replace('.', '', $valor);
        // troca vírgula por ponto
        $valor = str_replace(',', '.', $valor);
        $sala->valor_periodo = floatval($valor);
    } else {
        $sala->valor_periodo = 0;
    }

    $sala->periodo = 1;
    $sala->slug = $slug;
    $sala->status = 'ativa';

    $sala->data_inicial = Carbon::createFromFormat('d/m/Y', $request->get('data_inicial'));
    $sala->data_final = Carbon::createFromFormat('d/m/Y', $request->get('data_final'));

    if (!$sala->save()) {
        return false;
    } else {
        return $sala->id;
    }
}

    public function getSalas($termo_busca = null,$data_busca = null, $horario_busca = null) {

        $salas = DB::table('salas')->where("status","ativa")->get();
        $salas_ids = array();
        foreach ($salas as $s) {
            $salas_ids[] = $s->id;
        }

        if (!empty($horario_busca)) {

            $sala_model = new SalasOcorrenciasRepository();
            $index_horarios_sel = array_search($horario_busca,$sala_model->horarios_funcionamento);

            if (!empty($data_busca)) {
                $data = Carbon::createFromFormat('d/m/Y',$data_busca);
                $where = [["data",$data->format('Y-m-d')]];
            }

            $ocupacoes = DB::table("salas_ocorrencias")
                ->select("sala_id")
                ->where($where)
                ->whereIn("hora",[$horario_busca.":00",$sala_model->horarios_funcionamento[$index_horarios_sel+1].":00"])
                ->groupBy('sala_id')
                ->get()
                ->toArray();

            $sala_ocupada = [];
            foreach ($ocupacoes as $sala_ocorrencia) {
                $sala_ocupada[] = $sala_ocorrencia->sala_id;
            }

            foreach ($salas_ids as $k => $sala) {
                if (in_array($sala, $sala_ocupada)) {
                    unset($salas_ids[$k]);
                }
            }

        } elseif (!empty($data_busca)) {
            $data = Carbon::createFromFormat('d/m/Y',$data_busca);
            $ocupacoes = DB::select("select sala_id,count(*) as total from salas_ocorrencias where data = '".$data->format('Y-m-d')."' group by sala_id");
            $salas_ocupadas = array();
            foreach ($ocupacoes as $o) {
                if ($o->total >= 13) {
                    // Certeza que não tem horário
                    // Porem é possível ocupar uma salas com 9 horários
                    $salas_ocupadas[] = $o->sala_id;
                }
            }
            $salas_ids = array_diff($salas_ids,$salas_ocupadas);
        }

        $salas_qb = DB::table('salas')
            ->whereIn('salas.id',$salas_ids)
            ->select('salas.*');

        if (!empty($termo_busca)) {
            $salas_qb->where(
                function ($query) use ($termo_busca){
                    $query->where('salas.nome','like',"%{$termo_busca}%")
                        ->orWhere('salas.descricao','like',"%{$termo_busca}%")
                        ->orWhere('salas.numero','like',"%{$termo_busca}%");
                }
            );
        }

        $salas = $salas_qb->orderBy('salas.numero','asc')->get();

        foreach ($salas as $k => $s) {
            $capa = DB::table('salas_imagens')
                ->select('salas_imagens.*')
                ->where([
                    ['sala_id',$s->id],
                    ['tipo','capa']
                    ])
                ->first();
            $salas[$k]->capa = null;
            if (!empty($capa)) {
                $salas[$k]->capa = $capa->caminho;
            }
        }
        return $salas;
    }

    public function getSala($id) {
        $sala = DB::table('salas')
            ->select('salas.*')
            ->where('salas.id',$id)
            ->first();
        $capa = DB::table('salas_imagens')
            ->select('salas_imagens.*')
            ->where([
                ['sala_id',$sala->id],
                ['tipo','capa']
                ])
            ->first();
        $sala->capa = null;
        if (!empty($capa)) {
            $sala->capa = $capa->caminho;
        }
        return $sala;
    }


    public function getTotalSalas() {
        $sala = DB::table('salas')->where('status','ativa')->count();
        return $sala;
    }

    public function getTodas() {
        $salas = DB::table('salas')
            ->select('salas.*')
            ->where('status','ativa')
            ->orderBy('numero','asc')
            ->get();
        return $salas;
    }

    public function desativar($id) {

        $sala = $this->model->find($id);
        $sala->status = 'desativada';
        $sala->slug = \App\Helper\Funcoes::instance()->slugfy($sala->nome."_desativada");
        $sala->save();

        return true;
    }


}
