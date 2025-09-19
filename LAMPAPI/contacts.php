<?php
/*
ChatGPT generated file to match openapi.yaml 
and provide contacts methods boilerplate

9/4/2025
*/

require 'contactfunctions.php'; // ✅ use DB-backed functions
header("Content-Type: application/json");

$method  = $_SERVER['REQUEST_METHOD'];
$request = trim($_SERVER['REQUEST_URI']);
$base    = "/LAMPAPI/contacts";  
$path    = substr($request, strlen($base));

// ------------------- ROUTES -------------------

if ($method === 'GET' && $path === '') {
    // GET /contacts
    $result = getAllContacts();
    echo json_encode($result);
}
elseif ($method === 'POST' && $path === '') {
    // POST /contacts
    $data = json_decode(file_get_contents("php://input"), true);
    $result = addContact($data);
    echo json_encode($result);
}
elseif ($method === 'POST' && $path === '/bulk') {
    // POST /contacts/bulk
    $data = json_decode(file_get_contents("php://input"), true);
    $result = addContactsBulk($data);
    echo json_encode($result);
}
elseif ($method === 'GET' && preg_match('#^/(\d+)$#', $path, $matches)) {
    // GET /contacts/{id}
    $id = (int)$matches[1];
    $result = getContactById($id);
    echo json_encode($result);
}
elseif ($method === 'PUT' && preg_match('#^/(\d+)$#', $path, $matches)) {
    // PUT /contacts/{id}
    $id = (int)$matches[1];
    $data = json_decode(file_get_contents("php://input"), true);
    $result = updateContact($id, $data);
    echo json_encode(["results" => $result ? "Contact updated" : "Update failed"]);
}
elseif ($method === 'DELETE' && preg_match('#^/(\d+)$#', $path, $matches)) {
    // DELETE /contacts/{id}
    $id = (int)$matches[1];
    $result = deleteContact($id);
    echo json_encode(["results" => $result ? "Contact deleted" : "Delete failed"]);
}
else {
    http_response_code(404);
    echo json_encode(["error" => "Not Found"]);
}
