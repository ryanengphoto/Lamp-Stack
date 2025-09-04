<?php
/*
ChatGPT generated file to match openapi.yaml 
and provide user methods boilerplate

Fill in functions below

9/4/2025
*/

require 'db.php';
header("Content-Type: application/json");

$method  = $_SERVER['REQUEST_METHOD'];
$request = trim($_SERVER['REQUEST_URI']);
$base    = "/LAMPAPI/user";  // adjust if your base path changes
$path    = substr($request, strlen($base));

if ($method === 'POST' && $path === '') {
    // POST /user → create user
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode(["message" => "User created", "user" => $data]);
}
elseif ($method === 'GET' && preg_match('#^/([^/]+)$#', $path, $matches)) {
    // GET /user/{username}
    $username = $matches[1];
    echo json_encode(["id" => 1, "username" => $username]);
}
elseif ($method === 'DELETE' && preg_match('#^/([^/]+)$#', $path, $matches)) {
    // DELETE /user/{username}
    $username = $matches[1];
    echo json_encode(["message" => "User $username deleted"]);
}
else {
    http_response_code(404);
    echo json_encode(["error" => "Not Found"]);
}
