<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmacaoAgendamento;
use App\Mail\CancelamentoAgendamento;
use App\Mail\BoasVindasMedico;

class EmailEmergencyModule
{
    /**
     * Envia e-mail de confirmação (Síncrono)
     */
    public static function enviarConfirmacao($params, $email_destino)
    {
        try {
            $email_destino = strtolower(trim($email_destino));
            Log::info("Acionando Módulo de Emergência: Confirmação para $email_destino");
            
            Mail::to($email_destino)->send(new ConfirmacaoAgendamento($params));
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erro no Módulo de Emergência (Confirmacao) para $email_destino: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envia e-mail de cancelamento (Síncrono)
     */
    public static function enviarCancelamento($params, $email_destino)
    {
        try {
            $email_destino = strtolower(trim($email_destino));
            Log::info("Acionando Módulo de Emergência: Cancelamento para $email_destino");
            
            Mail::to($email_destino)->send(new CancelamentoAgendamento($params));
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erro no Módulo de Emergência (Cancelamento) para $email_destino: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envia e-mail de boas-vindas (Síncrono)
     */
    public static function enviarBoasVindas($params, $email_destino)
    {
        try {
            $email_destino = strtolower(trim($email_destino));
            Log::info("Acionando Módulo de Emergência: Boas-vindas para $email_destino");
            
            Mail::to($email_destino)->send(new BoasVindasMedico($params));
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erro no Módulo de Emergência (Boas-vindas) para $email_destino: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Teste de sanidade do módulo
     */
    public static function test($email_destino)
    {
        try {
            $email_destino = strtolower(trim($email_destino));
            Mail::raw('Módulo de Emergência Medical Place - Teste Síncrono', function ($m) use ($email_destino) {
                $m->to($email_destino)->subject('Teste de Módulo Isolado');
            });
            return "OK: Email direto enviado com sucesso para $email_destino";
        } catch (\Exception $e) {
            return "ERRO no módulo: " . $e->getMessage();
        }
    }
}
