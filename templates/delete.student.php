<?php
session_start();
require_once '../includes/db.php';

// Controleer of de gebruiker is ingelogd en een docent is
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Verwijder de student
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Student verwijderd.";
    } else {
        echo "Fout bij verwijderen van student.";
    }
} else {
    echo "Geen student ID opgegeven.";
}

?>

<p><a href="teacher_dashboard.php">Terug naar dashboard</a></p>
