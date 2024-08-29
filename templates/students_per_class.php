<?php
session_start();
require_once '../includes/db.php';

// Controleer of de gebruiker is ingelogd en een docent is
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Verkrijg alle klassen voor de dropdown
$classesStmt = $pdo->query("SELECT id, name FROM classes");
$classes = $classesStmt->fetchAll(PDO::FETCH_ASSOC);

// Verkrijg studenten per geselecteerde klas
$students = [];
if (isset($_GET['class_id']) && !empty($_GET['class_id'])) {
    $class_id = $_GET['class_id'];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE class_id = :class_id");
    $stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Studenten per Klas</title>
</head>
<body>
    <h1>Studenten per Klas</h1>
    <form action="students_per_class.php" method="GET">
        <label for="class_id">Klas:</label>
        <select id="class_id" name="class_id" required onchange="this.form.submit()">
            <option value="">Selecteer een klas</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo htmlspecialchars($class['id']); ?>" <?php echo (isset($_GET['class_id']) && $_GET['class_id'] == $class['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($class['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if (!empty($students)): ?>
        <h2>Studenten in Klas <?php echo htmlspecialchars($_GET['class_id']); ?></h2>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Naam</th>
                <th>Email</th>
                <th>Acties</th>
            </tr>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['id']); ?></td>
                <td><?php echo htmlspecialchars($student['name']); ?></td>
                <td><?php echo htmlspecialchars($student['email']); ?></td>
                <td>
                    <a href="edit_student.php?id=<?php echo htmlspecialchars($student['id']); ?>">Bewerken</a>
                    <a href="delete_student.php?id=<?php echo htmlspecialchars($student['id']); ?>" onclick="return confirm('Weet je zeker dat je deze student wilt verwijderen?');">Verwijderen</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Geen studenten gevonden voor deze klas.</p>
    <?php endif; ?>

    <p><a href="teacher_dashboard.php">Terug naar dashboard</a></p>
</body>
</html>
