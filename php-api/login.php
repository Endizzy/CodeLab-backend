<?php
// Разрешаем CORS
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// Отдаём 200 OK для preflight (OPTIONS) запроса
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Подключаем JWT библиотеку

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Настройки JWT
$jwtConfig = require_once __DIR__ . "/config/jwt.php";
$secretKey = $jwtConfig['secret']; // Замени на более надёжный ключ
$issuer = "localhost";
$expireTime = 3600; // токен будет действовать 1 час

// Получаем JSON из тела запроса
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'], $data['password'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Email и пароль обязательны"]);
    exit;
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($data['password'], $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Неверный email или пароль"]);
        exit;
    }

    $payload = [
        "iss" => $issuer,
        "aud" => $issuer,
        "iat" => time(),
        "exp" => time() + $expireTime,
        "data" => [
            "id" => $user['id'],
            "email" => $user['email'],
            "nickname" => $user['nickname'],
            "name_tag" => $user['name_tag'],
            "isAdmin" => $user['is_admin']
        ]
    ];

    $jwt = JWT::encode($payload, $secretKey, 'HS256');

    echo json_encode([
        "success" => true,
        "message" => "Авторизация успешна",
        "token" => $jwt
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Ошибка сервера: " . $e->getMessage()]);
}
