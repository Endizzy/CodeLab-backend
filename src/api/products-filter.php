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
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/db.php';

try {
    $pdo = Database::getConnection();

    // Получаем фильтры из запроса
    $type = $_GET['type'] ?? 'ALL';
    $category = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? null;

    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND title LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    switch (strtoupper($type)) {
        case 'POPULAR':
            $sql .= " ORDER BY views DESC";
            break;

        case 'NEWEST':
            $sql .= " ORDER BY createdAt DESC";
            break;

        case 'CATEGORY':
            if ($category) {
                $sql .= " AND category = :category ORDER BY createdAt DESC";
                $params[':category'] = $category;
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Category not specified']);
                exit();
            }
            break;

        case 'ALL':
        default:
            $sql .= " ORDER BY id DESC";
            break;
    }

    $sql .= " LIMIT 100";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
