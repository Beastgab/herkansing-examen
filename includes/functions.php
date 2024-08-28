<?php

function check_login($pdo, $role) {
    // Check of de gebruiker is ingelogd
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != $role) {
        header("Location: index.php");
        exit();
    }
}

function login($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $username);
    $stmt->execute();

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['roles'] = $user['roles'];
        return true;
    }

    return false;
}

function logout() {
    session_start();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Voeg hier meer functies toe zoals 'addTeacher', 'editTeacher', enz.
?>
