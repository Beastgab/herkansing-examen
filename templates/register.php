<?php
require_once '../includes/db.php';  // Verbind met de database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['roles']; // Zorg ervoor dat dit overeenkomt met de naam in je formulier en database

    if (!empty($email) && !empty($password) && !empty($role)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (email, password, roles) VALUES (:email, :password, :roles)");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':roles', $role); // Bind de 'roles' parameter

            if ($stmt->execute()) {
                echo "Gebruiker geregistreerd.";
            } else {
                echo "Fout bij registratie.";
            }
        } catch (PDOException $e) {
            echo "Databasefout: " . $e->getMessage();
        }
    } else {
        echo "Vul alle velden in.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registratie</title>
</head>
<body>
    <form action="register.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Wachtwoord:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="roles">Rol:</label>
        <select name="roles" id="roles" required>
            <option value="docent">Docent</option>
            <option value="roostermaker">Roostermaker</option>
            <option value="admin">Admin</option>
            <option value="student">Student</option>
            <option value="manager">manager</option>
        </select>
        <br>
        <input type="submit" value="Registreer">
    </form>
</body>
</html>
