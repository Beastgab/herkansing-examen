<?php
session_start();
require_once '../includes/db.php';

// Zorg ervoor dat alle PHP-fouten worden weergegeven
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Controleer of de gebruiker is ingelogd en een docent is
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Verkrijg de informatie van de ingelogde docent
$user_id = $_SESSION['user_id'];
$userStmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$userStmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$userStmt->execute();
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

// Verkrijg alle klassen voor de dropdown
$classesStmt = $pdo->query("SELECT id, name FROM classes");
$classes = $classesStmt->fetchAll(PDO::FETCH_ASSOC);

// Verkrijg studenten per geselecteerde klas
$selectedClassId = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;
$students = [];
if ($selectedClassId) {
    $studentsStmt = $pdo->prepare("SELECT students.id, students.name, students.email 
                                  FROM students 
                                  WHERE class_id = :class_id");
    $studentsStmt->bindParam(':class_id', $selectedClassId, PDO::PARAM_INT);
    $studentsStmt->execute();
    $students = $studentsStmt->fetchAll(PDO::FETCH_ASSOC);
}

// Verwerk toevoegen van student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $class_id = $_POST['class_id'];

    $stmt = $pdo->prepare("INSERT INTO students (name, email, class_id) VALUES (:name, :email, :class_id)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Student toegevoegd.";
        header("Location: teacher_dashboard.php?class_id=" . urlencode($class_id)); // Verwijst terug naar de dashboard met de geselecteerde klas
        exit();
    } else {
        echo "Fout bij toevoegen van student.";
    }
}

// Verwerk verwijderen van student
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Student verwijderd.";
        header("Location: teacher_dashboard.php?class_id=" . urlencode($selectedClassId)); // Verwijst terug naar de dashboard met de geselecteerde klas
        exit();
    } else {
        echo "Fout bij verwijderen van student.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Docent Dashboard</title>
</head>
<body>
    <h1>Docent Dashboard</h1>

    <!-- Gebruikersinformatie weergeven -->
    <div>
        <p>Welkom, <?php echo htmlspecialchars($user['name']); ?>!</p>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
    </div>

    <h2>Selecteer een Klas om te Bekijken</h2>
    <form action="teacher_dashboard.php" method="GET">
        <label for="class_id">Klas:</label>
        <select id="class_id" name="class_id" onchange="this.form.submit()">
            <option value="">Selecteer een klas</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo htmlspecialchars($class['id']); ?>" 
                        <?php echo ($selectedClassId == $class['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($class['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($selectedClassId): ?>
        <h2>Studenten Overzicht voor Klas ID <?php echo htmlspecialchars($selectedClassId); ?></h2>
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
                <a href="edit_student.php?id=<?php echo urlencode($student['id']); ?>">Bewerken</a>
                <a href="teacher_dashboard.php?delete_id=<?php echo htmlspecialchars($student['id']); ?>&class_id=<?php echo htmlspecialchars($selectedClassId); ?>" onclick="return confirm('Weet je zeker dat je deze student wilt verwijderen?');">Verwijderen</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <h2>Voeg een Nieuwe Student Toe</h2>
    <form action="teacher_dashboard.php" method="POST">
        <input type="hidden" name="add_student" value="1">
        <label for="name">Naam:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="class_id">Klas:</label>
        <select id="class_id" name="class_id" required>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo htmlspecialchars($class['id']); ?>">
                    <?php echo htmlspecialchars($class['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" value="Toevoegen">
    </form>

    <p><a href="add_class.php">Voeg een nieuwe klas toe</a></p>
    <p><a href="edit_profile.php">Bewerk je profiel</a></p>
    <p><a href="logout.php">Uitloggen</a></p>
</body>
</html>
