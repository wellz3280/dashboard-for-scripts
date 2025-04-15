<?php
$logPath = __DIR__ . '/app.log';

if (!file_exists($logPath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Log não encontrado']);
    exit;
}

$lines = file($logPath); // lê todas as linhas do log
$logs = [];

foreach ($lines as $line) {
    $entry = json_decode(trim($line), true);
    if ($entry) {
        $logs[] = $entry;
    }
}

header('Content-Type: application/json');
echo json_encode($logs);
