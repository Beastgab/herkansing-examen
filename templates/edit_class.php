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
    //var_dump($_GET); // Debugging: 

    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $class = $stmt->fetch(PDO::FETCH_ASSOC);
    //var_dump($class); // Debugging: Controleer de opgehaalde gegevens

    if (!$class) {
        echo "Klas niet gevonden.";
        exit();
    }
}
// Verwerk het formulier bij een POST-verzoek
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //var_dump($_POST); // Debugging: Controleer de POST-gegevens

    $id = $_POST['id'];
    $name = $_POST['name'];

    $stmt = $pdo->prepare("UPDATE classes SET name = :name WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $msg = "Klas bijgewerkt.";
        // Redirect naar de bewerkpagina met ID in de URL
        header("Location: edit_class.php?id=" . urlencode($id) . '&message='.$msg);
        exit();
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
    <form action="edit_class.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($class['id']); ?>">
        <label for="name">Naam:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($class['name']); ?>" required>
        <br>
        <input type="submit" value="Bijwerken">
    </form>
    <p><a href="teacher_dashboard.php">Terug naar dashboard</a></p>
    

</body>
</html>
