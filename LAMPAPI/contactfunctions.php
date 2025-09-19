<?php
/*
contactsfunctions.php
Boilerplate DB functions for contacts endpoint

👉 Fill in DB connection + query logic where TODO is
*/

// Example: include your DB connection
// require_once 'db.php';


/**
 * Get all contacts
 *
 * @return array
 */
function getAllContacts()
{
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error)
    {
        return ["results" => "", "errors" => [$conn->connect_error]];
    }

    $sql = "SELECT ID, FirstName, LastName, Phone, Email, UserID FROM Contacts";
    $result = $conn->query($sql);

    if (!$result)
    {
        $conn->close();
        return ["results" => "", "errors" => [$conn->error]];
    }

    $contacts = [];
    while ($row = $result->fetch_assoc())
    {
        $contacts[] = 
        [
            "id" => (int) $row["ID"],
            "firstName" => $row["FirstName"],
            "lastName" => $row["LastName"],
            "phone" => $row["Phone"],
            "email" => $row["Email"],
            "userId" => (int) $row["UserID"]
        ];
    }

    $result->free();
    $conn->close();

    return ["results" => $contacts, "errors" => []];
}

/**
 * Get single contact by ID
 *
 * @param int $id
 * @return array|null
 */
function getContactById($id)
{
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error)
    {
        return ["results" => "", "error" => $conn->connect_error];
    }

    $stmt = $conn->prepare("SELECT ID, FirstName, LastName, Phone, Email, UserID FROM Contacts WHERE ID = ?");
    if (!$stmt)
    {
        $conn->close();
        return ["results" => null, "errors" => [$conn->error]];
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $contact = null;
    if ($row = $result->fetch_assoc())
    {
        $contact = 
        [
            "id" => (int) $row["ID"], 
            "firstName" => $row["FirstName"],
            "lastName" => $row["LastName"],
            "phone" => $row["Phone"],
            "email" => $row["Email"],
            "userId" => $row["UserID"]
        ];
    }

    $result->free();
    $stmt->close();
    $conn->close();
    

    return ["results" => $contact, "errors" => []];
}

/**
 * search contacts with string
 * 
 * @param array $data
 * @return array
 */
function searchContacts($data)
{

    $search = "%" . $data["search"] . "%";
    $userId = (int)$data["userId"];

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error)
    {
        return ["results" => [], "error" => $conn->connect_error];
    }
    else
    {
        $stmt = $conn->prepare("SELECT ID, FirstName, LastName, Phone, Email
                FROM  Contacts
                WHERE (FirstName LIKE ? OR LastName LIKE ? OR Phone LIKE ? OR Email LIKE ?) AND UserID = ?");
        $stmt->bind_param("ssssi", $search, $search, $search, $search, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $searchResults = [];
        while ($row = $result->fetch_assoc()) 
        {
            $searchResults[] =
            [
                "id" => (int) $row["ID"], 
                "firstName" => $row["FirstName"],
                "lastName" => $row["LastName"],
                "phone" => $row["Phone"],
                "email" => $row["Email"]
            ];
        }
        
        $stmt->close();
        $conn->close();
        
        return ["results" => $searchResults, "error" => ""];
    }
}

/**
 * search contacts with string
 * 
 * @param array $data
 * @return array
 */
function searchContacts($data)
{

    $search = "%" . $data["search"] . "%";
    $userId = (int)$data["userId"];

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error)
    {
        return ["results" => [], "error" => $conn->connect_error];
    }
    else
    {
        $stmt = $conn->prepare("SELECT ID, FirstName, LastName, Phone, Email
                FROM  Contacts
                WHERE (FirstName LIKE ? OR LastName LIKE ? OR Phone LIKE ? OR Email LIKE ?) AND UserID = ?");
        $stmt->bind_param("ssssi", $search, $search, $search, $search, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $searchResults = [];
        while ($row = $result->fetch_assoc()) 
        {
            $searchResults[] =
            [
                "id" => (int) $row["ID"], 
                "firstName" => $row["FirstName"],
                "lastName" => $row["LastName"],
                "phone" => $row["Phone"],
                "email" => $row["Email"]
            ];
        }
        
        $stmt->close();
        $conn->close();
        
        return ["results" => $searchResults, "error" => ""];
    }
}

/**
 * Add a new contact
 *
 * @param array $data
 * @return array
 */
function addContact($data) {

    $firstName = $data["firstName"];
    $lastName = $data["lastName"];
    $phone = $data["phone"];
    $email = $data["email"];
    $userId = (int)($data["userId"]);

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error)
    {
        return ["results" => "", "error" => $conn->connect_error];
    }
    else
    {
        $stmt = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $firstName, $lastName, $phone, $email, $userId);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        return ["results" => "Contact added successfully.", "error" => ""];
    }

}

/**
 * Bulk add contacts
 *
 * @param array $contacts
 * @return array
 */
function addContactsBulk($contacts)
{
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error)
    {
        return ["results" => "", "errors" => [$conn->connect_error]];
    }

    $stmt = $conn->prepare
    (
        "INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID) VALUES (?, ?, ?, ?, ?)"
    );

    if (!$stmt)
    {
        $conn->close();
        return ["results" => "", "errors" => [$conn->error]];
    }

    $numSuccess = 0;
    $errors = [];

    //add contacts one by one
    foreach($contacts as $c)
    {
        $firstName = $c["firstName"];
        $lastName = $c["lastName"];
        $phone = $c["phone"];
        $email = $c["email"];
        $userId = (int) $c["userId"];

        $stmt->bind_param("ssssi", $firstName, $lastName, $phone, $email, $userId);

        if ($stmt->execute())
        {
            $numSuccess++;
        }
        else
        {
            $errors[] = ["contact" => $c, "error" => $stmt->error];
        }
    }

    $stmt->close();
    $conn->close();


    return ["results" => "$numSuccess contacts were added successfully", "errors" => $errors];
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
