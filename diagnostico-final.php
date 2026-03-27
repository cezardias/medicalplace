<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "--- INICIANDO TESTE DE EMAIL (SYNCHRONOUS) ---\n";

try {
    $email = 'automedicalplace@gmail.com'; // Test email
    $params = [
        'nome' => 'Teste Antigravity',
        'sala' => 'SALA TESTE-01',
        'data' => date('d/m/Y'),
        'horarios' => ['08:00', '08:30']
    ];

    echo "Tentando enviar CONFIRMACAO para $email...\n";
    $sent = Mail::send('emails.confirmacao_agendamento', ['params' => $params], function ($m) use ($email) {
        $m->to($email)->subject('TESTE DE CONFIRMACAO');
    });
    echo "Resultado: " . ($sent ? "SUCESSO" : "FALHA (Retornou false)") . "\n";

} catch (\Exception $e) {
    echo "ERRO FATAL: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "--- LOGS RECENTES (ÚLTIMAS 10 LINHAS) ---\n";
$logFile = storage_path('logs/laravel-' . date('Y-m-d') . '.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    echo implode("", array_slice($lines, -10));
} else {
    echo "Arquivo de log não encontrado: $logFile\n";
}
