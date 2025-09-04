<?php
/*
ChatGPT generated file to match openapi.yaml 
and provide contacts methods boilerplate

9/4/2025
*/

require 'db.php';
header("Content-Type: application/json");

$method  = $_SERVER['REQUEST_METHOD'];
$request = trim($_SERVER['REQUEST_URI']);
$base    = "/LAMPAPI/contacts";  
$path    = substr($request, strlen($base));

if ($method === 'GET' && $path === '') {
    // GET /contacts
    echo json_encode([["id" => 1, "firstName" => "John", "lastName" => "James"]]);
}
elseif ($method === 'POST' && $path === '') {
    // POST /contacts
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode(["message" => "Contact added", "contact" => $data]);
}
elseif ($method === 'POST' && $path === '/bulk') {
    // POST /contacts/bulk
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode(["message" => "Bulk add success", "contacts" => $data]);
}
elseif ($method === 'GET' && preg_match('#^/(\d+)$#', $path, $matches)) {
    // GET /contacts/{id}
    $id = $matches[1];
    echo json_encode(["id" => $id, "firstName" => "Jane", "lastName" => "Doe"]);
}
elseif ($method === 'PUT' && preg_match('#^/(\d+)$#', $path, $matches)) {
    // PUT /contacts/{id}
    $id = $matches[1];
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode(["message" => "Contact updated", "id" => $id, "newData" => $data]);
}
elseif ($method === 'DELETE' && preg_match('#^/(\d+)$#', $path, $matches)) {
    // DELETE /contacts/{id}
    $id = $matches[1];
    echo json_encode(["message" => "Contact $id deleted"]);
}
else {
    http_response_code(404);
    echo json_encode(["error" => "Not Found"]);
}
