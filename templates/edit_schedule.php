<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Controleer of de gebruiker is ingelogd en een roostermaker is
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'scheduler') {
    header('Location: login.php');
    exit();
}

require_once '../includes/db.php';

// Verkrijg de gegevens van het rooster om te bewerken
$schedule = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$schedule) {
        echo "Rooster niet gevonden.";
        exit();
    }
} else {
    echo "Geen rooster ID opgegeven.";
    exit();
}

// Verkrijg de waarde van de dashboard-parameter of stel een standaard waarde in
$dashboardLink = isset($_GET['dashboard']) ? htmlspecialchars($_GET['dashboard']) : 'scheduler_dashboard.php';

// Verwerk het formulier bij een POST-verzoek
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $schedule_date = $_POST['schedule_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    try {
        $stmt = $pdo->prepare("UPDATE schedules SET class_id = :class_id, subject_id = :subject_id, schedule_date = :schedule_date, start_time = :start_time, end_time = :end_time WHERE id = :id");
        $stmt->bindParam(':class_id', $class_id);
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->bindParam(':schedule_date', $schedule_date);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo "Rooster bijgewerkt.";
        } else {
            echo "Fout bij bijwerken van rooster.";
        }
    } catch (PDOException $e) {
        echo "Fout bij SQL: " . $e->getMessage();
    }
}

// Verkrijg de beschikbare klassen en vakken
$classesStmt = $pdo->query("SELECT id, name FROM classes");
$classes = $classesStmt->fetchAll(PDO::FETCH_ASSOC);

$subjectsStmt = $pdo->query("SELECT id, name FROM subjects");
$subjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bewerk Rooster</title>
</head>
<body>
    <h1>Bewerk Rooster</h1>
    <form action="edit_schedule.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($schedule['id']); ?>">
        <label for="class_id">Klas:</label>
        <select id="class_id" name="class_id" required>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo htmlspecialchars($class['id']); ?>" <?php if ($class['id'] == $schedule['class_id']) echo 'selected'; ?>><?php echo htmlspecialchars($class['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="subject_id">Vak:</label>
        <select id="subject_id" name="subject_id" required>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?php echo htmlspecialchars($subject['id']); ?>" <?php if ($subject['id'] == $schedule['subject_id']) echo 'selected'; ?>><?php echo htmlspecialchars($subject['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="schedule_date">Datum:</label>
        <input type="date" id="schedule_date" name="schedule_date" value="<?php echo htmlspecialchars($schedule['schedule_date']); ?>" required>
        <br>
        <label for="start_time">Starttijd:</label>
        <input type="time" id="start_time" name="start_time" value="<?php echo htmlspecialchars($schedule['start_time']); ?>" required>
        <br>
        <label for="end_time">Eindtijd:</label>
        <input type="time" id="end_time" name="end_time" value="<?php echo htmlspecialchars($schedule['end_time']); ?>" required>
        <br>
        <input type="submit" value="Bijwerken">
    </form>
    <p><a href="<?php echo $dashboardLink; ?>">Terug naar dashboard</a></p>
</body>
</html>
