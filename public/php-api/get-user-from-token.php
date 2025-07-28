<?php
header("Access-Control-Allow-Origin: https://codelab-frontend-production.up.railway.app");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Если это preflight запрос, отвечаем 200 и завершаем скрипт
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';
$jwtConfig = require_once __DIR__ . '/config/jwt.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$secretKey = $jwtConfig['secret'] ?? null;

if (!$secretKey) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "JWT секретный ключ не задан"]);
    exit;
}

// Получаем заголовок Authorization
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Не передан токен"]);
    exit;
}

$token = $matches[1];

try {
    $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
    $userData = (array)$decoded->data;

    echo json_encode([
        "success" => true,
        "user" => $userData
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Неверный или просроченный токен"]);
}
