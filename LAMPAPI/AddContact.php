<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$inData = getRequestInfo();

$firstName = $inData["firstName"];
$lastName = $inData["lastName"];
$phone = $inData["phone"];
$email = $inData["email"];
$userId = (int)($inData["userId"]);

$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
if ($conn->connect_error)
{
	returnWithError($conn->connect_error);
}
else
{
	$stmt = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID) VALUES (?, ?, ?, ?, ?)");
	$stmt->bind_param("ssssi", $firstName, $lastName, $phone, $email, $userId);
	$stmt->execute();
	$stmt->close();
	$conn->close();

	returnWithInfo("Contact added successfully.");
}

function getRequestInfo()
{
	return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson($obj)
{
	header('Content-type: application/json');
	echo $obj;
}

function returnWithError($err)
{
	$retValue = '{"error":"' . $err . '"}';
	sendResultInfoAsJson($retValue);
}

function returnWithInfo($searchResults)
{
	$retValue = '{"results":"' .  $searchResults . '","error":""}';
	sendResultInfoAsJson($retValue);
}

?>
