<?php
/*
ChatGPT generated file to match openapi.yaml
User endpoint handler

Fill in DB logic where TODO notes are

9/4/2025
*/

//require 'db.php';
require_once 'userfunctions.php';

header("Content-Type: application/json");
// 🔹 Allow CORS for Swagger UI/browser testing
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Figure out method + path
$method  = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];

// adjust base path to match your API root
$base = "/LAMPAPI/user"; // base for /user routes

// strip query string
if (strpos($request, '?') !== false) {
    $request = strtok($request, '?');
}

$path = substr($request, strlen($base));
$pathLogin = substr($request, strlen($baseLogin));

// --- Routing ---

// Create user
if ($method === 'POST' && $path === '') {
    $data = json_decode(file_get_contents("php://input"), true);
    $newUser = createUser($data);

    if (isset($newUser['error'])) {
        http_response_code(500);
        echo json_encode($newUser); // send DB error
    } else {
        echo json_encode([
            "message" => "User created",
            "user"    => $newUser
        ]);
    }
}

// Get user by login
elseif ($method === 'GET' && preg_match('#^/([^/]+)$#', $path, $matches)) {
    $login = $matches[1];
    // TODO: Replace with DB lookup
    echo json_encode([
        "id"    => 1,
        "login" => $login,
        "email" => "test@example.com"
    ]);
}

// Delete user by login
elseif ($method === 'DELETE' && preg_match('#^/([^/]+)$#', $path, $matches)) {
    $login = $matches[1];
    $result = deleteUser($login);
    if (isset($result['error'])) {
        http_response_code(404);
    }
    echo json_encode($result);
}

// --- LOGIN route ---
// POST /login (preferred)
elseif ($method === 'POST' && $request === $base . '/login') {
    $data = json_decode(file_get_contents("php://input"), true);
    $login = $data['login'] ?? '';
    $password = $data['password'] ?? '';

    $user = loginUser($login, $password);

    if ($user) {
        echo json_encode([
            "message" => "Login successful",
            "user"    => $user
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Invalid credentials"]);
    }
}

// GET /login?login=...&password=... (for testing only ⚠️ insecure)
elseif ($method === 'GET' && $request === $baseLogin) {
    $login = $_GET['login'] ?? '';
    $password = $_GET['password'] ?? '';

    $user = loginUser($login, $password);

    if ($user) {
        echo json_encode([
            "message" => "Login successful",
            "user"    => $user
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Invalid credentials"]);
    }
}

// Not found
else {
    http_response_code(404);
    echo json_encode([
        "error"  => "Not Found",
        "path"   => $path,
        "method" => $method
    ]);
}
