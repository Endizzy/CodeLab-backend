<?php
// Точка входа, которая перенаправляет запросы в API

$requestUri = $_SERVER['REQUEST_URI'];

// Простейшая маршрутизация: все запросы начинаются с /api
if (strpos($requestUri, '/api') === 0) {
    // Убираем /api из начала и подключаем нужный скрипт из src/api
    $script = __DIR__ . '/../src/api' . substr($requestUri, 4);
    if (file_exists($script)) {
        require $script;
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'API endpoint not found']);
    }
    exit;
} else {
    echo json_encode(['message' => 'Welcome to CodeLab Backend']);
}
