<?php
session_start();

if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['role'], ['scheduler', 'admin'])) {
    header('Location: login.php');
    exit();
}

require_once '../includes/db.php';

// Verkrijg de waarde van de dashboard-parameter of stel een standaard waarde in
$dashboardLink = isset($_GET['dashboard']) ? htmlspecialchars($_GET['dashboard']) : 'scheduler_dashboard.php';

// Verwerk het formulier bij een POST-verzoek
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $schedule_date = $_POST['schedule_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    try {
        if (empty($class_id) || empty($subject_id) || empty($schedule_date) || empty($start_time) || empty($end_time)) {
            throw new Exception('Vul alle velden in.');
        }

        $stmt = $pdo->prepare("INSERT INTO schedules (class_id, subject_id, schedule_date, start_time, end_time) VALUES (:class_id, :subject_id, :schedule_date, :start_time, :end_time)");
        $stmt->bindParam(':class_id', $class_id);
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->bindParam(':schedule_date', $schedule_date);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);

        if ($stmt->execute()) {
            $message = "Rooster toegevoegd.";
        } else {
            $message = "Fout bij toevoegen van rooster.";
        }
    } catch (Exception $e) {
        $message = "Fout: " . $e->getMessage();
    } catch (PDOException $e) {
        $message = "Fout bij SQL: " . $e->getMessage();
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
    <title>Voeg Rooster Toe</title>
</head>
<body>
    <h1>Voeg Rooster Toe</h1>

    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="add_schedule.php" method="POST">
        <label for="class_id">Klas:</label>
        <select id="class_id" name="class_id" required>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo htmlspecialchars($class['id']); ?>"><?php echo htmlspecialchars($class['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="subject_id">Vak:</label>
        <select id="subject_id" name="subject_id" required>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?php echo htmlspecialchars($subject['id']); ?>"><?php echo htmlspecialchars($subject['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="schedule_date">Datum:</label>
        <input type="date" id="schedule_date" name="schedule_date" required>
        <br>
        <label for="start_time">Starttijd:</label>
        <input type="time" id="start_time" name="start_time" required>
        <br>
        <label for="end_time">Eindtijd:</label>
        <input type="time" id="end_time" name="end_time" required>
        <br>
        <input type="submit" value="Toevoegen">
    </form>
    <p><a href="<?php echo $dashboardLink; ?>">Terug naar dashboard</a></p>
</body>
</html>
