<?php
/*
contactsfunctions.php
Boilerplate DB functions for contacts endpoint

👉 Fill in DB connection + query logic where TODO is
*/

// Example: include your DB connection
// require_once 'db.php';


//General functions to be used by contact functions:
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
//end of general functions


/**
 * Get all contacts
 *
 * @return array
 */
function getAllContacts() {
    // TODO: replace with real DB query
    // Example:
    // global $db;
    // $stmt = $db->query("SELECT id, firstName, lastName, email, phone FROM contacts");
    // return $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        ["id" => 1, "firstName" => "John", "lastName" => "James"],
        ["id" => 2, "firstName" => "Jane", "lastName" => "Doe"]
    ];
}

/**
 * Get single contact by ID
 *
 * @param int $id
 * @return array|null
 */
function getContactById($id) {
    // TODO: replace with real DB query
    // Example:
    // global $db;
    // $stmt = $db->prepare("SELECT * FROM contacts WHERE id = ?");
    // $stmt->execute([$id]);
    // return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    return ["id" => $id, "firstName" => "Jane", "lastName" => "Doe"];
}

/**
 * search contacts with string
 * 
 * @param json $data
 * @return json indirectly via sendResultInfoAsJson()
 */
function searchContacts($data)
{

    $search = "%" . $data["search"] . "%";
    $userId = (int)$data["userId"];

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
}

/**
 * Add a new contact
 *
 * @param json $data
 * @return json indirectly via sendResultInfoAsJson()
 */
function addContact($data) {
    // TODO: replace with real DB insert
    // Example:
    // global $db;
    // $stmt = $db->prepare("INSERT INTO contacts (firstName, lastName, email, phone) VALUES (?, ?, ?, ?)");
    // $stmt->execute([$data['firstName'], $data['lastName'], $data['email'], $data['phone']]);
    // return ["id" => $db->lastInsertId()] + $data;

    $firstName = $data["firstName"];
    $lastName = $data["lastName"];
    $phone = $data["phone"];
    $email = $data["email"];
    $userId = (int)($data["userId"]);

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

}

/**
 * Bulk add contacts
 *
 * @param array $contacts
 * @return array
 */
function addContactsBulk($contacts) {
    // TODO: loop + insert into DB
    // Example:
    // foreach ($contacts as $c) { ... }
    return $contacts;
}

/**
 * Update contact by ID
 *
 * @param int $id
 * @param array $data
 * @return bool
 */
function updateContact($id, $data) {
    // TODO: replace with real DB update
    // Example:
    // global $db;
    // $stmt = $db->prepare("UPDATE contacts SET firstName=?, lastName=?, email=?, phone=? WHERE id=?");
    // return $stmt->execute([...]);

    return true; // placeholder
}

/**
 * Delete contact by ID
 *
 * @param int $id
 * @return bool
 */
function deleteContact($id) {
    // TODO: replace with real DB delete
    // Example:
    // global $db;
    // $stmt = $db->prepare("DELETE FROM contacts WHERE id=?");
    // return $stmt->execute([$id]);

    return true; // placeholder
}
