<?php
require_once '../includes/db.php';

session_start();

// Controleer of de gebruiker een manager is
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'manager') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Zorg ervoor dat je id als integer verwerkt

    // Verwijder de docent
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Na succesvolle verwijdering, herlaad het dashboard met een succesmelding
        header('Location: manager_dashboard.php?status=deleted');
        exit();
    } else {
        echo "Fout bij verwijderen van docent.";
    }
} else {
    echo "Geen docent ID opgegeven.";
}
?>
