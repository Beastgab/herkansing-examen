<?php
session_start();
require_once '../includes/db.php';

// Controleer of de gebruiker is ingelogd en een docent is
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Verwerk formulier bij een POST-verzoek
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $user_id = $_SESSION['user_id']; // De docent wordt de eigenaar van de klas

    $stmt = $pdo->prepare("INSERT INTO classes (name, teacher_id) VALUES (:name, :teacher_id)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':teacher_id', $user_id);

    if ($stmt->execute()) {
        echo "Klas toegevoegd.";
    } else {
        echo "Fout bij toevoegen van klas.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Voeg Klas Toe</title>
</head>
<body>
    <h1>Voeg Klas Toe</h1>
    <form action="add_class.php" method="POST">
        <label for="name">Naam van de klas:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <input type="submit" value="Toevoegen">
    </form>
    <p><a href="teacher_dashboard.php">Terug naar dashboard</a></p>
</body>
</html>
