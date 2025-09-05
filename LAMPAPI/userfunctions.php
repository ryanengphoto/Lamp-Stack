<?php
/*
userfunctions.php
Boilerplate DB functions for user endpoint

👉 Fill in DB connection + query logic where TODO is
*/

// Example: include your DB connection
// require_once 'db.php';

/**
 * Create a new user
 *
 * @param array $data  User data (expects keys like username, email, password, etc.)
 * @return array|false Returns inserted user info or false on failure
 */
function createUser($data) {
    // TODO: replace with real DB insert
    // Example:
    // global $db;
    // $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    // $stmt->execute([$data['username'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT)]);
    // return [ "id" => $db->lastInsertId(), "username" => $data['username'], "email" => $data['email'] ];

    return [
        "id"       => 1,
        "username" => $data['username'] ?? null,
        "email"    => $data['email'] ?? null
    ];
}

/**
 * Login user (verify username + password)
 *
 * @param string $username
 * @param string $password
 * @return array|null Returns user record if valid, null otherwise
 */
function loginUser($username, $password) {
    // TODO: replace with real DB lookup + password_verify
    // Example:
    // global $db;
    // $stmt = $db->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
    // $stmt->execute([$username]);
    // $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // if ($user && password_verify($password, $user['password'])) {
    //     unset($user['password']); // don’t leak hash
    //     return $user;
    // }
    // return null;

    // placeholder
    if ($username === "test" && $password === "1234") {
        return [
            "id"       => 1,
            "username" => "test",
            "email"    => "test@example.com"
        ];
    }
    return null;
}

/**
 * Delete user
 *
 * @param string $username
 * @return bool True if deleted, false otherwise
 */
function deleteUser($username) {
    // TODO: replace with real DB delete
    // Example:
    // global $db;
    // $stmt = $db->prepare("DELETE FROM users WHERE username = ?");
    // return $stmt->execute([$username]);

    return true; // placeholder
}
