<?php

// Script de Diagnóstico de E-mail Isolado
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\EmailEmergencyModule;
use Illuminate\Support\Facades\Mail;

$email_teste = 'cezar.dias@gmail.com'; // Altere se necessário

echo "--- INICIANDO TESTE DE EMAIL ISOLADO ---\n";

// 1. TESTE RAW (SANITY)
try {
    echo "1. Enviando RAW... ";
    Mail::raw('Teste Raw Medical Place', function($m) use ($email_teste) {
        $m->to($email_teste)->subject('Teste RAW');
    });
    echo "OK\n";
} catch (\Exception $e) {
    echo "FALHA: " . $e->getMessage() . "\n";
}

// 2. TESTE CANCELAMENTO (O QUE FUNCIONA)
echo "2. Enviando CANCELAMENTO (O que funciona)... ";
$res = EmailEmergencyModule::enviarCancelamento([
    'medico' => 'Medico Teste',
    'sala' => 'Consultorio 01',
    'data' => date('d/m/Y'),
    'horarios' => ['08:00', '08:30']
], $email_teste);
echo ($res ? "OK (veja se chegou)" : "FALHA") . "\n";

// 3. TESTE CONFIRMACAO
echo "3. Enviando CONFIRMACAO (O problematico)... ";
$res = EmailEmergencyModule::enviarConfirmacao([
    'medico' => 'Medico Teste',
    'sala' => 'Consultorio 01',
    'data' => date('d/m/Y'),
    'horarios' => ['08:00', '08:30'],
    'valor_total' => 40.00
], $email_teste);
echo ($res ? "OK (veja se chegou)" : "FALHA") . "\n";

// 4. TESTE BOAS-VINDAS
echo "4. Enviando BOAS-VINDAS... ";
$res = EmailEmergencyModule::enviarBoasVindas([
    'nome' => 'Medico Teste',
    'email' => $email_teste
], $email_teste);
echo ($res ? "OK (veja se chegou)" : "FALHA") . "\n";

echo "--- FIM DO TESTE ---\n";
echo "Verifique os logs em storage/logs/laravel-".date('Y-m-d').".log para detalhes.\n";
