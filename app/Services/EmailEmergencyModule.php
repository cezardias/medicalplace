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
            'mail.mailers.smtp.username' => 'naoresponder@medicalplace.med.br',
            'mail.mailers.smtp.password' => 'm3d1c4lpl4c3@',
            'mail.from.address' => 'naoresponder@medicalplace.med.br',
            'mail.from.name' => 'Medical Place'
        ]);
    }

    public static function enviarConfirmacao($params, $email_destino)
    {
        try {
            self::setSMTP();
            $email_destino = strtolower(trim($email_destino));
            Log::info("EMERGENCY-MAIL: Enviando CONFIRMACAO (RAW) para $email_destino");
            
            $body = "Agendamento Confirmado!\n\nSala: " . ($params['sala'] ?? 'N/A') . "\nData: " . ($params['data'] ?? 'N/A') . "\nHorário: " . ($params['horario'] ?? 'N/A');
            
            \Mail::raw($body, function ($m) use ($email_destino) {
                $m->from('naoresponder@medicalplace.med.br', 'Medical Place');
                $m->to($email_destino)->subject('Agendamento Medical Place');
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
            Log::info("EMERGENCY-MAIL: Enviando CANCELAMENTO para $email_destino");
            
            \Mail::send('emails.cancelamento_agendamento', ['params' => $params], function ($m) use ($email_destino) {
                $m->from('naoresponder@medicalplace.med.br', 'Medical Place');
                $m->to($email_destino)->subject('Agendamento Medical Place');
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
            Log::info("EMERGENCY-MAIL: Enviando BOAS-VINDAS (RAW) para $email_destino");
            
            $body = "Bem-vindo a Medical Place!\n\nSeu cadastro foi realizado com sucesso.\nEmail: " . ($params['email'] ?? 'N/A');
            
            \Mail::raw($body, function ($m) use ($email_destino) {
                $m->from('naoresponder@medicalplace.med.br', 'Medical Place');
                $m->to($email_destino)->subject('Acesso Medical Place');
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error("EMERGENCY-MAIL: ERRO Boas-vindas: " . $e->getMessage());
            return false;
        }
    }
}
