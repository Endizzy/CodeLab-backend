<?php
$allowedOrigins = [
    'http://localhost:3000',
    'http://127.0.0.1:3000',
    'https://codelab-frontend-production.up.railway.app'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

// header("Access-Control-Allow-Origin: https://codelab-frontend-production.up.railway.app");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/db.php';

try {
    $pdo=Database::getConnection();
    $stmt = $pdo->query("SELECT * FROM products");

    $products = $stmt->fetchAll();

    header('Content-Type: application/json');
    echo json_encode($products);

    } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
}

