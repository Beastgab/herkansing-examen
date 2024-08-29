<?php
session_start();
require_once '../includes/db.php';

// Controleer of de gebruiker is ingelogd en een docent is
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Verkrijg de ID van de klas en verwijder deze
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM classes WHERE id = :id AND teacher_id = :teacher_id");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':teacher_id', $_SESSION['user_id']);

    if ($stmt->execute()) {
        header('Location: teacher_dashboard.php');
        exit();
    } else {
        echo "Fout bij verwijderen van klas.";
    }
} else {
    echo "Geen klas ID opgegeven.";
}
