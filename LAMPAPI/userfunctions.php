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
    // TODO: replace with real DB lookup + password_verify
    // Example:
    // global $db;
    // $stmt = $db->prepare("SELECT id, login, firstName, lastName, password FROM Users WHERE login = ?");
    // $stmt->execute([$login]);
    // $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // if ($user && password_verify($password, $user['password'])) {
    //     unset($user['password']); // don’t leak hash
    //     return $user;
    // }
    // return null;

    // placeholder
    if ($login === "test" && $password === "1234") {
        return [
            "id"        => 1,
            "login"  => "test",
            "firstName" => "Test",
            "lastName"  => "User"
        ];
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
    // TODO: replace with real DB delete
    // Example:
    // global $db;
    // $stmt = $db->prepare("DELETE FROM Users WHERE login = ?");
    // return $stmt->execute([$login]);

    return true; // placeholder
}
