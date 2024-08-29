<?php
session_start();
require_once '../includes/db.php';

// Controleer of de gebruiker is ingelogd en een docent is
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Verkrijg de class gegevens om te bewerken
$class = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Debugging: Controleer de waarde van $id
    var_dump($id);

    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debugging: Controleer de opgehaalde gegevens
    var_dump($class);

    if (!$class) {
        echo "Klas niet gevonden.";
        exit();
    }
} else {
    echo "Geen klas ID opgegeven.";
    exit();
}

// Verwerk het formulier bij een POST-verzoek
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];

    // Debugging: Controleer de POST-gegevens
    var_dump($_POST);

    $stmt = $pdo->prepare("UPDATE classes SET name = :name WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Klas bijgewerkt.";
    } else {
        echo "Fout bij bijwerken van klas.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bewerk Klas</title>
</head>
<body>
    <h1>Bewerk Klas</h1>
    <form action="edit_class.php" method="POST" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($class['id']); ?>">
        <label for="name">Naam:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($class['name']); ?>" required autocomplete="off">
        <br>
        <input type="submit" value="Bijwerken">
    </form>
    <p><a href="teacher_dashboard.php">Terug naar dashboard</a></p>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('form').addEventListener('submit', function(e) {
            var id = document.querySelector('input[name="id"]').value;
            var action = this.action;
            if (!action.includes('id=')) {
                this.action += (action.includes('?') ? '&' : '?') + 'id=' + encodeURIComponent(id);
            }
        });
    });
    </script>
</body>
</html>