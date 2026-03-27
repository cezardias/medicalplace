<?php

namespace App\Services;

use Log;
use Mail;
use App\Mail\ConfirmacaoAgendamento;
use App\Mail\CancelamentoAgendamento;
use App\Mail\BoasVindasMedico;

class EmailEmergencyModule
{
    /**
     * Envia e-mail de confirmação (Forçando modo síncrono para garantir entrega na Hostinger)
     */
    public static function enviarConfirmacao($params, $email_destino)
    {
        try {
            Log::info("Acionando Módulo de Emergência: Confirmação para $email_destino");
            
            // Usamos 'send' ao invés de 'queue' para ignorar o worker quebrado
            Mail::to($email_destino)->send(new ConfirmacaoAgendamento($params));
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erro no Módulo de Emergência (Confirmacao): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envia e-mail de cancelamento (Síncrono)
     */
    public static function enviarCancelamento($params, $email_destino)
    {
        try {
            Log::info("Acionando Módulo de Emergência: Cancelamento para $email_destino");
            
            Mail::to($email_destino)->send(new CancelamentoAgendamento($params));
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erro no Módulo de Emergência (Cancelamento): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envia e-mail de boas-vindas (Síncrono)
     */
    public static function enviarBoasVindas($params, $email_destino)
    {
        try {
            Log::info("Acionando Módulo de Emergência: Boas-vindas para $email_destino");
            
            Mail::to($email_destino)->send(new BoasVindasMedico($params));
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erro no Módulo de Emergência (Boas-vindas): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Teste de sanidade do módulo
     */
    public static function test($email_destino)
    {
        try {
            Mail::raw('Módulo de Emergência Medical Place - Teste Síncrono', function ($m) use ($email_destino) {
                $m->to($email_destino)->subject('Teste de Módulo Isolado');
            });
            return "OK: Email direto enviado com sucesso para $email_destino";
        } catch (\Exception $e) {
            return "ERRO no módulo: " . $e->getMessage();
        }
    }
}
