<?php
session_start();
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'scheduler') {
    header('Location: login.php');
    exit();
}

require_once '../includes/db.php';


// Verkrijg alle roosters
$stmt = $pdo->query("SELECT schedules.id, classes.name as class_name, subjects.name as subject_name, schedule_date, start_time, end_time
                     FROM schedules
                     JOIN classes ON schedules.class_id = classes.id
                     JOIN subjects ON schedules.subject_id = subjects.id
                     ORDER BY schedule_date ASC, start_time ASC");
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Scheduler Dashboard</title>
</head>
<body>
    <h1>Roostermaker Dashboard</h1>

    <h2>Roosters voor de Eerstvolgende Week</h2>
    <table border="1">
        <tr>
            <th>Klas</th>
            <th>Vak</th>
            <th>Datum</th>
            <th>Starttijd</th>
            <th>Eindtijd</th>
            <th>Acties</th>
        </tr>
        <?php foreach ($schedules as $schedule): ?>
        <tr>
            <td><?php echo htmlspecialchars($schedule['class_name']); ?></td>
            <td><?php echo htmlspecialchars($schedule['subject_name']); ?></td>
            <td><?php echo htmlspecialchars($schedule['schedule_date']); ?></td>
            <td><?php echo htmlspecialchars($schedule['start_time']); ?></td>
            <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
            <td>
                <a href="edit_schedule.php?id=<?php echo htmlspecialchars($schedule['id']); ?>">Bewerken</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="add_schedule.php">Voeg een nieuw rooster toe</a></p>
    <p><a href="logout.php">Uitloggen</a></p>
</body>
</html>
