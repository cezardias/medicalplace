<?php

// Script de Diagnóstico de E-mail Isolado (V2 - Padronizado)
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\EmailEmergencyModule;
use Illuminate\Support\Facades\Mail;

$email_teste = 'cezar.dias@gmail.com'; 

echo "--- INICIANDO TESTE DE EMAIL ISOLADO (V2) ---\n";

// 1. TESTE RAW
try {
    echo "1. Enviando RAW... ";
    Mail::raw('Teste Raw Medical Place', function($m) use ($email_teste) {
        $m->to($email_teste)->subject('Teste RAW');
    });
    echo "OK\n";
} catch (\Exception $e) {
    echo "FALHA: " . $e->getMessage() . "\n";
}

// 2. TESTE CANCELAMENTO
echo "2. Enviando CANCELAMENTO... ";
$res = EmailEmergencyModule::enviarCancelamento([
    'nome' => 'Medico Teste (Cancelamento)',
    'sala' => 'Consultorio 01',
    'data' => date('d/m/Y'),
    'horarios' => ['08:00', '08:30']
], $email_teste);
echo ($res ? "OK" : "FALHA") . "\n";

// 3. TESTE CONFIRMACAO
echo "3. Enviando CONFIRMACAO... ";
$res = EmailEmergencyModule::enviarConfirmacao([
    'nome' => 'Medico Teste (Confirmacao)',
    'sala' => 'Consultorio 01',
    'data' => date('d/m/Y'),
    'horarios' => ['08:00', '08:30'],
    'valor_total' => 40.00
], $email_teste);
echo ($res ? "OK" : "FALHA") . "\n";

// 4. TESTE BOAS-VINDAS
echo "4. Enviando BOAS-VINDAS... ";
$res = EmailEmergencyModule::enviarBoasVindas([
    'nome' => 'Medico Teste (Boas-vindas)',
    'email' => $email_teste
], $email_teste);
echo ($res ? "OK" : "FALHA") . "\n";

echo "--- FIM DO TESTE ---\n";
echo "Verifique storage/logs/laravel-".date('Y-m-d').".log se houver falhas.\n";
