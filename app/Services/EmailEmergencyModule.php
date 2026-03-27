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
            'mail.mailers.smtp.port' => 587,
            'mail.mailers.smtp.encryption' => 'tls',
            'mail.mailers.smtp.username' => 'naoresponder@medicalplace.med.br',
            'mail.mailers.smtp.password' => 'm3d1c4lpl4c3@',
            'mail.from.address' => 'naoresponder@medicalplace.med.br',
            'mail.from.name' => 'Medical Place',
        ]);
    }

    public static function enviarConfirmacao($params, $email_destino)
    {
        try {
            self::setSMTP();
            $email_destino = strtolower(trim($email_destino));
            $id = time();
            Log::info("EMERGENCY-MAIL: Enviando CONFIRMACAO [$id] para $email_destino");
            
            \Mail::send('emails.confirmacao_agendamento', ['params' => $params], function ($m) use ($email_destino, $id) {
                $m->from('naoresponder@medicalplace.med.br', 'Medical Place');
                $m->to($email_destino)->subject("Agendamento Medical Place #$id");
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error("EMERGENCY-MAIL: ERRO Confirmação: " . $e->getMessage());
            return false;
        }
    }

    public static function enviarCancelamento($params, $email_destino)
    {
        try {
            self::setSMTP();
            $email_destino = strtolower(trim($email_destino));
            $id = time();
            Log::info("EMERGENCY-MAIL: Enviando CANCELAMENTO [$id] para $email_destino");
            
            \Mail::send('emails.cancelamento_agendamento', ['params' => $params], function ($m) use ($email_destino, $id) {
                $m->from('naoresponder@medicalplace.med.br', 'Medical Place');
                $m->to($email_destino)->subject("Agendamento Medical Place #$id");
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error("EMERGENCY-MAIL: ERRO Cancelamento: " . $e->getMessage());
            return false;
        }
    }

    public static function enviarBoasVindas($params, $email_destino)
    {
        try {
            self::setSMTP();
            $email_destino = strtolower(trim($email_destino));
            $id = time();
            Log::info("EMERGENCY-MAIL: Enviando BOAS-VINDAS [$id] para $email_destino");
            
            \Mail::send('emails.boas_vindas_medico', ['params' => $params], function ($m) use ($email_destino, $id) {
                $m->from('naoresponder@medicalplace.med.br', 'Medical Place');
                $m->to($email_destino)->subject("Acesso Medical Place #$id");
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error("EMERGENCY-MAIL: ERRO Boas-vindas: " . $e->getMessage());
            return false;
        }
    }
}
