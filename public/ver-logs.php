<?php
$logFile = __DIR__ . '/../storage/logs/laravel-' . date('Y-m-d') . '.log';
header('Content-Type: text/plain');
if (file_exists($logFile)) {
    echo "--- LOG DE HOJE (" . date('Y-m-d') . ") ---\n";
    $content = file($logFile);
    echo implode("", array_slice($content, -100)); // Last 100 lines
} else {
    echo "Log não encontrado em: $logFile\n";
    echo "Arquivos no diretório logs:\n";
    if (is_dir(__DIR__ . '/../storage/logs/')) {
        print_r(scandir(__DIR__ . '/../storage/logs/'));
    } else {
        echo "Pasta de logs não existe ou sem permissão.";
    }
}
