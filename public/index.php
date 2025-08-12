<?php
$allowedOrigins = [
    'http://localhost:3000',
    'http://127.0.0.1:3000',
    'https://codelab-frontend-production.up.railway.app'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($request) {
    case '/':
        echo json_encode(['message' => 'Welcome to CodeLab Backend']);
        break;

    case '/login':
        require __DIR__ . '/../src/api/login.php';
        break;

    case '/register':
    case '/registration':
        require __DIR__ . '/../src/api/registration.php';
        break;

    case '/get-user-from-token':
        require __DIR__ . '/../src/api/get-user-from-token.php';
        break;

    case '/get_new_users':
        require __DIR__ . '/../src/api/get_new_users.php';
        break;

    case '/get-products':
        require __DIR__ . '/../src/api/get-products.php';
        break;

    case '/products-filter':
        require __DIR__ . '/../src/api/products-filter.php';
        break;

    case '/get-all-users':
        require __DIR__ . '/../src/api/get-all-users.php';
        break;

    case '/update-user':
        require __DIR__ . '/../src/api/update-user.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
}
