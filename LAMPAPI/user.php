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
$base = "/LAMPAPI/user";

// strip query string
if (strpos($request, '?') !== false) {
    $request = strtok($request, '?');
}

$path = substr($request, strlen($base));

// --- Routing ---
if ($method === 'POST' && $path === '') {
    // POST /user → create user
    $data = json_decode(file_get_contents("php://input"), true);

    $newUser = createUser($data);

    if ($newUser) {
        echo json_encode([
            "message" => "User created",
            "user"    => $newUser
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to create user"]);
    }
}

elseif ($method === 'GET' && preg_match('#^/([^/]+)$#', $path, $matches)) {
    // GET /user/{username}
    $username = $matches[1];
    // TODO: Lookup from DB
    echo json_encode([
        "id"       => 1,
        "username" => $username,
        "email"    => "test@example.com"
    ]);
}
elseif ($method === 'DELETE' && preg_match('#^/([^/]+)$#', $path, $matches)) {
    // DELETE /user/{username}
    $username = $matches[1];
    // TODO: Delete from DB
    echo json_encode([
        "message" => "User $username deleted"
    ]);
}
else {
    http_response_code(404);
    echo json_encode([
        "error" => "Not Found",
        "path"  => $path,
        "method"=> $method
    ]);
}
