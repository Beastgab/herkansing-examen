<?php
require_once '../includes/db.php';  // Controleer of dit pad correct is
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];  // Verander 'username' naar 'email'
    $password = $_POST['password'];
    $role = 'teacher'; // Rol instellen op 'teacher'

    // Controleer of de POST-gegevens aanwezig zijn
    if (!empty($email) && !empty($password)) {
        // Wachtwoord hashen
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Voer de query uit
        $stmt = $pdo->prepare("INSERT INTO users (email, password, roles) VALUES (:email, :password, :roles)");
        $stmt->bindParam(':email', $email);  // Pas 'username' aan naar 'email'
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':roles', $role);  // Bind de rol variabele correct

        if ($stmt->execute()) {
            echo "Docent toegevoegd.";
        } else {
            echo "Fout bij het toevoegen van docent.";
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
    <title>Docent Toevoegen</title>
</head>
<body>
    <h1>Nieuwe Docent Toevoegen</h1>
    <form action="add_teacher.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Wachtwoord:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" value="Toevoegen">
    </form>
</body>
</html>

