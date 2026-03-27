<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\EmailEmergencyModule;
use Illuminate\Support\Facades\Log;

echo "--- INICIANDO TESTE DE E-MAIL V3 ---" . PHP_EOL;

// 1. Teste de Cancelamento (O que funciona)
echo "1. Enviando CANCELAMENTO... ";
$params1 = ['nome' => 'Teste Marcelo', 'sala' => 'Sala Teste', 'data' => '2026-03-27', 'horario' => '10:00'];
$res1 = EmailEmergencyModule::enviarCancelamento($params1, 'naoresponder@medicalplace.med.br'); // Enviando para si mesmo para testar
echo ($res1 ? "OK" : "FALHA") . PHP_EOL;

// 2. Teste de Confirmação
echo "2. Enviando CONFIRMACAO... ";
$params2 = ['nome' => 'Teste Marcelo', 'sala' => 'Sala Teste', 'data' => '2026-03-27', 'horario' => '10:00', 'valor' => 100];
$res2 = EmailEmergencyModule::enviarConfirmacao($params2, 'naoresponder@medicalplace.med.br');
echo ($res2 ? "OK" : "FALHA") . PHP_EOL;

// 3. Teste de Boas-Vindas
echo "3. Enviando BOAS-VINDAS... ";
$params3 = ['nome' => 'Teste Marcelo', 'email' => 'teste@teste.com'];
$res3 = EmailEmergencyModule::enviarBoasVindas($params3, 'naoresponder@medicalplace.med.br');
echo ($res3 ? "OK" : "FALHA") . PHP_EOL;

echo "--- FIM DOS TESTES ---" . PHP_EOL;
echo "Verifique se chegaram 3 e-mails na conta naoresponder@medicalplace.med.br" . PHP_EOL;
