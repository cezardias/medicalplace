<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailEmergencyModule
{
    /**
     * Configura o SMTP em tempo de execução para bypass de cache (Hostinger/Titan)
     */
    private static function setSMTP()
    {
        // Usamos as credenciais do .env que estão na maquina do usuario
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.titan.email',
            'mail.mailers.smtp.port' => 465,
            'mail.mailers.smtp.encryption' => 'ssl',
            'mail.mailers.smtp.username' => 'naoresponda@medicalplace.med.br',
            'mail.mailers.smtp.password' => 'm3d1c4lpl4c3@',
            'mail.from.address' => 'naoresponda@medicalplace.med.br',
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
            Log::info("DEBUG-MAIL: Enviando Confirmação para $email_destino");
            
            Mail::send('emails.confirmacao_agendamento', ['params' => $params], function ($m) use ($email_destino) {
                $m->from(config('mail.from.address'), config('app.name', 'Medical Place'));
                $m->to($email_destino)->subject('Agendamento Confirmado - Medical Place');
            });
            
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
            Log::info("DEBUG-MAIL: Enviando Cancelamento para $email_destino");
            
            Mail::send('emails.cancelamento_agendamento', ['params' => $params], function ($m) use ($email_destino) {
                $m->from(config('mail.from.address'), config('app.name', 'Medical Place'));
                $m->to($email_destino)->subject('Agendamento Cancelado - Medical Place');
            });
            
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
            Log::info("DEBUG-MAIL: Enviando Boas-vindas para $email_destino");
            
            Mail::send('emails.boas_vindas_medico', ['params' => $params], function ($m) use ($email_destino) {
                $m->from(config('mail.from.address'), config('app.name', 'Medical Place'));
                $m->to($email_destino)->subject('Bem-vindo a Medical Place!');
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error("DEBUG-MAIL: ERRO Boas-vindas: " . $e->getMessage());
            return false;
        }
    }
}
