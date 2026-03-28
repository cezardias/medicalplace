<?php

// Script para ver logs de pagamento de hoje
// Uso: php artisan tinker ver-logs.php

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$hoje = Carbon::now()->format('Y-m-d');
$log_file = storage_path("logs/laravel-{$hoje}.log");

echo "=== LOGS DE HOJE ($hoje) ===\n";

if (file_exists($log_file)) {
    // Buscar linhas que contenham PagSeguro ou reference_id
    $content = file_get_contents($log_file);
    $lines = explode("\n", $content);
    $found = false;
    foreach ($lines as $line) {
        if (strpos($line, 'PAGSEGURO') !== false || strpos($line, 'reference_id') !== false || strpos($line, 'EMERGENCY-MAIL') !== false) {
            echo $line . "\n";
            $found = true;
        }
    }
    if (!$found) echo "Nenhum log de pagamento encontrado no arquivo de hoje.\n";
} else {
    echo "Arquivo de log não encontrado: $log_file\n";
    echo "Dica: Verifique se existem outros logs em storage/logs/: \n";
    foreach (glob(storage_path("logs/*.log")) as $file) {
        echo "- " . basename($file) . "\n";
    }
}

echo "\n=== TRANSAÇÕES NO BANCO (SUCESSO) ===\n";
$transacoes = DB::table('transacoes')
    ->whereDate('created_at', $hoje)
    ->get();

if ($transacoes->count() > 0) {
    foreach ($transacoes as $t) {
        echo "ID: {$t->id} | Valor: {$t->valor} | Status: {$t->status} | Data: {$t->created_at}\n";
    }
} else {
    echo "Nenhuma transação de sucesso registrada no banco hoje.\n";
}

echo "\n=== TENTATIVAS DE CRÉDITO (HISTÓRICO) ===\n";
$creditos = DB::table('creditos')
    ->whereDate('created_at', $hoje)
    ->get();

if ($creditos->count() > 0) {
    foreach ($creditos as $c) {
        echo "ID: {$c->id} | User: {$c->user_id} | Valor: {$c->valor} | Tipo: {$c->tipo} | Data: {$c->created_at}\n";
    }
} else {
    echo "Nenhuma movimentação de créditos encontrada hoje.\n";
}
