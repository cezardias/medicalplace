<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailEmergencyModule
{
    /**
     * Configura o SMTP em tempo de execução para bypass de cache (Hostinger)
     */
    private static function setSMTP()
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.hostinger.com',
            'mail.mailers.smtp.port' => 465,
            'mail.mailers.smtp.encryption' => 'ssl',
            'mail.mailers.smtp.username' => 'atendimento@medicalplace.med.br',
            'mail.mailers.smtp.password' => 'Marcelo@medical10#',
            'mail.from.address' => 'atendimento@medicalplace.med.br',
            'mail.from.name' => 'Medical Place'
        ]);
    }

    /**
     * Envia e-mail de confirmação (Síncrono)
     */
    public static function enviarConfirmacao($params, $email_destino)
    {
        try {
            self::setSMTP();
            $email_destino = strtolower(trim($email_destino));
            $log_msg = "[" . date('Y-m-d H:i:s') . "] Enviando CONFIRMACAO para: $email_destino\n";
            file_put_contents('/tmp/mail_debug.txt', $log_msg, FILE_APPEND);
            Log::info("DEBUG-MAIL: Enviando Confirmação para $email_destino");
            
            $sent = Mail::send('emails.confirmacao_agendamento', ['params' => $params], function ($m) use ($email_destino) {
                $m->from(config('mail.from.address'), config('app.name', 'Medical Place'));
                $m->to($email_destino)->subject('Agendamento Confirmado - Medical Place');
            });
            
            Log::info("DEBUG-MAIL: Resultado Confirmação: ENVIADO");
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
            self::setSMTP();
            $email_destino = strtolower(trim($email_destino));
            $log_msg = "[" . date('Y-m-d H:i:s') . "] Enviando CANCELAMENTO para: $email_destino\n";
            file_put_contents('/tmp/mail_debug.txt', $log_msg, FILE_APPEND);
            Log::info("DEBUG-MAIL: Enviando Cancelamento para $email_destino");
            
            $sent = Mail::send('emails.cancelamento_agendamento', ['params' => $params], function ($m) use ($email_destino) {
                $m->from(config('mail.from.address'), config('app.name', 'Medical Place'));
                $m->to($email_destino)->subject('Agendamento Cancelado - Medical Place');
            });
            
            Log::info("DEBUG-MAIL: Resultado Cancelamento: ENVIADO");
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
            self::setSMTP();
            $email_destino = strtolower(trim($email_destino));
            $log_msg = "[" . date('Y-m-d H:i:s') . "] Enviando BOAS-VINDAS para: $email_destino\n";
            file_put_contents('/tmp/mail_debug.txt', $log_msg, FILE_APPEND);
            Log::info("DEBUG-MAIL: Enviando Boas-vindas para $email_destino");
            
            $sent = Mail::send('emails.boas_vindas_medico', ['params' => $params], function ($m) use ($email_destino) {
                $m->from(config('mail.from.address'), config('app.name', 'Medical Place'));
                $m->to($email_destino)->subject('Bem-vindo a Medical Place!');
            });
            
            Log::info("DEBUG-MAIL: Resultado Boas-vindas: ENVIADO");
            return true;
        } catch (\Exception $e) {
            Log::error("DEBUG-MAIL: ERRO Boas-vindas: " . $e->getMessage());
            return false;
        }
    }
}
