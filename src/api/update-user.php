<?php

require_once __DIR__ . '/db.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Bad JSON']);
    exit;
}

if (!is_array($input) || !isset($input['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing user id']);
    exit;
}

$id = (int)$input['id'];

$allowedFields = ['email', 'nickname', 'name_tag', 'rank', 'is_admin'];
$setParts = [];
$params = [];

foreach ($allowedFields as $f) {
    if (array_key_exists($f, $input)) {
        $setParts[] = "$f = ?";
        if ($f === 'is_admin') {
            $params[] = (int)$input[$f];
        } elseif ($f === 'rank') {
            $params[] = (int)$input[$f];
        } else {
            $params[] = $input[$f];
        }
    }
}

if (empty($setParts)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No fields to update']);
    exit;
}

$params[] = $id;
$sql = 'UPDATE users SET ' . implode(', ', $setParts) . ' WHERE id = ?';

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute($params);

    if ($ok) {
        $select = $pdo->prepare('SELECT id, email, nickname, name_tag, published_scripts_count, total_code_views, rank, is_admin, created_at FROM users WHERE id = ?');
        $select->execute([$id]);
        $user = $select->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'user' => $user]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Update failed']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
    exit;
}
