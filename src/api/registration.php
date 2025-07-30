<?php
header("Access-Control-Allow-Origin: https://codelab-frontend-production.up.railway.app");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Обрабатываем preflight запрос OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config/db.php';

// Получаем сырые данные из тела запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email'], $data['password'], $data['nickname'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Не все поля заполнены']);
    exit;
}

$email = trim($data['email']);
$password = trim($data['password']);
$nickname = trim($data['nickname']);

// Валидация базовая
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Неверный формат email']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Пароль должен быть минимум 6 символов']);
    exit;
}

if (strlen($nickname) < 2) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Никнэйм слишком короткий']);
    exit;
}

try {
    $pdo = Database::getConnection();

    // Проверяем, есть ли уже такой email
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким email уже существует']);
        exit;
    }

    // Хешируем пароль
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $nameTag = '@' . explode('@', $email)[0];


    // Вставляем нового пользователя
    $isAdmin = 0;
    $stmt = $pdo->prepare('INSERT INTO users (nickname, email, password_hash, name_tag, created_at, is_admin) VALUES (?, ?, ?, ?, NOW(), ?)');
    $stmt->execute([$nickname, $email, $hashedPassword, $nameTag, $isAdmin]);


    echo json_encode(['success' => true, 'message' => 'Пользователь успешно зарегистрирован']);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера: ' . $e->getMessage()]);
}
