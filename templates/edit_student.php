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

// Debugging: controleer de GET-parameters
var_dump($_GET);

// Verkrijg de student gegevens om te bewerken
$student = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo "Student niet gevonden.";
        exit();
    }
}

// Verwerk het formulier bij een POST-verzoek
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $class_id = $_POST['class_id'];

    $stmt = $pdo->prepare("UPDATE students SET name = :name, email = :email, class_id = :class_id WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: teacher_dashboard.php?class_id=" . urlencode($class_id));
        exit();
    } else {
        echo "Fout bij bijwerken van student.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bewerk Student</title>
</head>
<body>
    <h1>Bewerk Student</h1>
    <form action="edit_student.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">
        <label for="name">Naam:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        <br>
        <label for="class_id">Klas:</label>
        <select id="class_id" name="class_id" required>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo htmlspecialchars($class['id']); ?>" <?php echo ($class['id'] == $student['class_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($class['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" value="Bijwerken">
    </form>
    <p><a href="teacher_dashboard.php?class_id=<?php echo urlencode($student['class_id']); ?>">Terug naar dashboard</a></p>
</body>
</html>
