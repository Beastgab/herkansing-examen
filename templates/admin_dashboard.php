<?php
session_start();

// Controleer of de gebruiker een manager is
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    //header('Location: login.php');  // Redirect naar inloggen als niet ingelogd of geen manager
    //exit();
}

require_once '../includes/db.php';  // Verbind met de database
require_once '../includes/functions.php';  // Voeg je functies toe als nodig

// Verkrijg lijst van docenten
$stmt = $pdo->query("SELECT * FROM users WHERE roles = 'teacher'");
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Manager Dashboard</title>
</head>
<body>
    <h1>Manager Dashboard</h1>

    <!-- Uitlogknop -->
    <form action="logout.php" method="POST">
        <input type="submit" value="Uitloggen">
    </form>

    <!-- Melding na verwijdering en update -->
    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] === 'deleted') {
            echo "<p>Docent succesvol verwijderd.</p>";
        } elseif ($_GET['status'] === 'updated') {
            echo "<p>Docent succesvol bijgewerkt.</p>";
        } elseif ($_GET['status'] === 'schedule_added') {
            echo "<p>Rooster succesvol toegevoegd.</p>";
        }
    }
    ?>

    <!-- Link naar de pagina om roosters toe te voegen -->
    <p><a href="add_schedule.php?dashboard=admin_dashboard.php">Voeg Rooster Toe</a></p>


    <h2>Overzicht van Docenten</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Acties</th>
        </tr>
        <?php foreach ($teachers as $teacher): ?>
        <tr>
            <td><?php echo htmlspecialchars($teacher['id']); ?></td>
            <td><?php echo htmlspecialchars($teacher['email']); ?></td>
            <td>
                <a href="edit_teacher.php?id=<?php echo htmlspecialchars($teacher['id']); ?>">Bewerken</a>
                <a href="delete_teacher.php?id=<?php echo htmlspecialchars($teacher['id']); ?>" onclick="return confirm('Weet je zeker dat je deze docent wilt verwijderen?');">Verwijderen</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
