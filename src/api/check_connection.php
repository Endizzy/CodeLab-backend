<?php
header("Access-Control-Allow-Origin: https://codelab-frontend-production.up.railway.app");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Обработка preflight-запроса от браузера
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/./config/db.php';

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'message' => 'Подключение к MySQL успешно',
        'tables' => $tables,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка подключения: ' . $e->getMessage()
    ]);
}
