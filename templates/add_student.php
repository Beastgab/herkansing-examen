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

// Verwerk het formulier bij een POST-verzoek
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $class_id = $_POST['class_id'];

    $stmt = $pdo->prepare("INSERT INTO students (name, email, class_id) VALUES (:name, :email, :class_id)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Student toegevoegd.";
    } else {
        echo "Fout bij toevoegen van student.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Voeg Student Toe</title>
</head>
<body>
    <h1>Voeg Student Toe</h1>
    <form action="add_student.php" method="POST">
        <label for="name">Naam:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="class_id">Klas:</label>
        <select id="class_id" name="class_id" required>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo htmlspecialchars($class['id']); ?>"><?php echo htmlspecialchars($class['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" value="Toevoegen">
    </form>
    <p><a href="teacher_dashboard.php">Terug naar dashboard</a></p>
</body>
</html>
