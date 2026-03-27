<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\DirectMail;

class EmailEmergencyModule
{
    /**
     * Envia e-mail de confirmação (Síncrono)
     */
    public static function enviarConfirmacao($params, $email_destino)
    {
        try {
            $email_destino = strtolower(trim($email_destino));
            Log::info("DEBUG-MAIL: Enviando Confirmação para $email_destino");
            
            $sent = Mail::to($email_destino)->send(new DirectMail($params, 'emails.confirmacao_agendamento', 'Agendamento Confirmado - Medical Place'));
            
            Log::info("DEBUG-MAIL: Resultado Confirmação: " . ($sent ? "ENVIADO" : "FALHA"));
            return true;
        } catch (\Exception $e) {
            Log::error("DEBUG-MAIL: ERRO Confirmação: " . $e->getMessage());
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
            Log::info("DEBUG-MAIL: Enviando Cancelamento para $email_destino");
            
            $sent = Mail::to($email_destino)->send(new DirectMail($params, 'emails.cancelamento_agendamento', 'Agendamento Cancelado - Medical Place'));
            
            Log::info("DEBUG-MAIL: Resultado Cancelamento: " . ($sent ? "ENVIADO" : "FALHA"));
            return true;
        } catch (\Exception $e) {
            Log::error("DEBUG-MAIL: ERRO Cancelamento: " . $e->getMessage());
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
            Log::info("DEBUG-MAIL: Enviando Boas-vindas para $email_destino");
            
            $sent = Mail::to($email_destino)->send(new DirectMail($params, 'emails.boas_vindas_medico', 'Bem-vindo a Medical Place!'));
            
            Log::info("DEBUG-MAIL: Resultado Boas-vindas: " . ($sent ? "ENVIADO" : "FALHA"));
            return true;
        } catch (\Exception $e) {
            Log::error("DEBUG-MAIL: ERRO Boas-vindas: " . $e->getMessage());
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
                $m->to($email_destino)->subject('Teste de Modulo Isolado');
            });
            return "OK: Email direto enviado com sucesso para $email_destino";
        } catch (\Exception $e) {
            return "ERRO no módulo: " . $e->getMessage();
        }
    }
}
