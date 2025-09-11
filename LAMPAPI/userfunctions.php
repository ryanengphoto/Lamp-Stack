<?php
/*
userfunctions.php
Boilerplate DB functions for user endpoint
*/

 // Example: include your DB connection
// require_once 'db.php';

/**
 * Create a new user
 *
 * @param array $data  User data (expects keys like login, firstName, lastName, password)
 * @return array|false Returns inserted user info or false on failure
 */
function createUser($data) {
    // Extract fields safely
    $login  = $data["login"]  ?? "";
    $firstName = $data["firstName"] ?? "";
    $lastName  = $data["lastName"]  ?? "";
    $password  = $data["password"]  ?? "";

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // DB connection
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error) {
        return false;
    }

    $checkStmt = $conn->prepare("SELECT ID FROM Users WHERE login = ?");
    $checkStmt->bind_param("s", $login);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
	    $checkStmt->close();
	    $conn->close();
	    return false;
    }
    $checkStmt->close();

    $stmt = $conn->prepare("
        INSERT INTO Users (login, FirstName, LastName, Password)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $login, $firstName, $lastName, $hashedPassword);

    if ($stmt->execute()) {
        $newId = $conn->insert_id;
        $stmt->close();
        $conn->close();

        return [
            "id"        => $newId,
            "login"  => $login,
            "firstName" => $firstName,
            "lastName"  => $lastName
        ];
    } else {
        $stmt->close();
        $conn->close();
        return false;
    }
}

/**
 * Login user (verify login + password)
 *
 * @param string $login
 * @param string $password
 * @return array|null Returns user record if valid, null otherwise
 */
function loginUser($login, $password) {
	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	if ($conn->connect_error) {
		return false;
	}

	$stmt = $conn->prepare("SELECT ID, login, firstName, lastName, password FROM Users WHERE login = ?");
	$stmt->bind_param("s", $login);
	$stmt->execute();
	$result = $stmt->get_result();

	$user = $result->fetch_assoc();
	$stmt->close();
	$conn->close();

	if ($user) {
		if (password_verify($password, $user['password'])) {
			unset($user['password']);
			return $user;
		}
	}

	return null;
}

/**
 * Delete user
 *
 * @param string $login
 * @return bool True if deleted, false otherwise
 */
function deleteUser($login) {
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error) {
	    return false;
    }

    $stmt = $conn->prepare("DELETE FROM Users WHERE login = ?");
    if (!$stmt) {
	    $conn->close();
	    return false;
    }

    $stmt->bind_param("s", $login);
    $success = $stmt->execute();

    $deleted = $success && ($stmt->affected_rows > 0);

    $stmt->close();
    $conn->close();

    return $deleted;
}

