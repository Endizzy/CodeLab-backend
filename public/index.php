<?php

$request = $_SERVER['REQUEST_URI'];

switch ($request) {
    case '/' :
        echo json_encode(['message' => 'Welcome to CodeLab Backend']);
        break;
    case '/login' :
        require __DIR__ . '/../src/api/login.php';
        break;
    case '/register' :
    case '/registration' :
        require __DIR__ . '/../src/api/registration.php';
        break;
    case '/get-user-from-token' :
        require __DIR__ . '/../src/api/get-user-from-token.php';
        break;
    case '/get_new_users' :
        require __DIR__ . '/../src/api/get_new_users.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
}
