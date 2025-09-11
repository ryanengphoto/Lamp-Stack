<?php
/*
userfunctions.php
Clean DB functions for user endpoint
*/

function createUser($data) {
    $login     = $data["login"] ?? "";
    $firstName = $data["firstName"] ?? "";
    $lastName  = $data["lastName"] ?? "";
    $password  = $data["password"] ?? ""; // plain text for now

    // Connect to DB
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error) {
        return ["error" => "DB Connection failed: " . $conn->connect_error];
    }

    // Prepare insert statement
    $stmt = $conn->prepare("
        INSERT INTO Users (login, FirstName, LastName, Password)
        VALUES (?, ?, ?, ?)
    ");
    if (!$stmt) {
        $conn->close();
        return ["error" => "Prepare failed: " . $conn->error];
    }

    $stmt->bind_param("ssss", $login, $firstName, $lastName, $password);

    if ($stmt->execute()) {
        $newId = $conn->insert_id;
        $stmt->close();
        $conn->close();
        return [
            "id"        => $newId,
            "login"     => $login,
            "firstName" => $firstName,
            "lastName"  => $lastName
        ];
    } else {
        $errorMsg = $stmt->error;
        $stmt->close();
        $conn->close();
        return ["error" => "Insert failed: " . $errorMsg];
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
		#if (password_verify($password, $user['password'])) {
        if ($password === $user['password']) {
			unset($user['password']);
			return $user;
		}
	}

	return null;
}

function deleteUser($login) {
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error) {
        return ["error" => "DB Connection failed: " . $conn->connect_error];
    }

    $stmt = $conn->prepare("DELETE FROM Users WHERE login = ?");
    if (!$stmt) {
        $conn->close();
        return ["error" => "Prepare failed: " . $conn->error];
    }

    $stmt->bind_param("s", $login);

    if ($stmt->execute()) {
        $affected = $stmt->affected_rows;
        $stmt->close();
        $conn->close();
        if ($affected > 0) {
            return ["message" => "User $login deleted"];
        } else {
            return ["error" => "User $login not found"];
        }
    } else {
        $errorMsg = $stmt->error;
        $stmt->close();
        $conn->close();
        return ["error" => "Delete failed: " . $errorMsg];
    }
}