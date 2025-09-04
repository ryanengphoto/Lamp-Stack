<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

$inData = getRequestInfo();

$search = "%" . $inData["search"] . "%";
$userId = (int)$inData["userId"];

$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
if ($conn->connect_error)
{
	returnWithError($conn->connect_error);
}
else
{
	$stmt = $conn->prepare("SELECT ID, FirstName, LastName, Phone, Email
			FROM  Contacts
			WHERE (FirstName LIKE ? OR LastName LIKE ? OR Phone LIKE ? OR Email LIKE ?) AND UserID = ?");
	$stmt->bind_param("ssssi", $search, $search, $search, $search, $userId);
	$stmt->execute();
	$result = $stmt->get_result();

	$searchResults = "";
	while ($row = $result->fetch_assoc()) 
	{
		if ($searchResults != "")
		{
			$searchResults .= ",";
		}
		$searchResults .= '{
			"id":' . $row["ID"] .
			',"firstName":"' . $row["FirstName"] . '"' .
			',"lastName":"' . $row["LastName"] . '"' .
			',"phone":"' . $row["Phone"] . '"' .
			',"email":"' . $row["Email"] . '"}';
	}
	
	
	$stmt->close();
	$conn->close();
	
	returnWithInfo($searchResults);
}

function getRequestInfo()
{
	return json_decode(file_get_contents('php://input'), true);
}

function sendResultInfoAsJson($obj)
{
	header("Content-type: application/json");
	echo $obj;
}

function returnWithError($err)
{
	$retValue = '{"results":[], "error":"' . $err . '"}';
	sendResultInfoAsJson($retValue);
}

function returnWithInfo($searchResults)
{
	$retValue = '{"results":[' . $searchResults . '],"error":""}';
	sendResultInfoAsJson($retValue);
}

?>
