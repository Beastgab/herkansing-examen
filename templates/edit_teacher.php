<?php
session_start();

// Controleer of de gebruiker een manager is
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect naar inloggen als niet ingelogd of geen manager
    exit();
}

require_once '../includes/db.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Als wachtwoord leeg is, wordt het niet gewijzigd

    // Begin met de SQL query
    $sql = "UPDATE users SET email = :email";
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = :password";
    }
    $sql .= " WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    if (!empty($password)) {
        $stmt->bindParam(':password', $hashed_password);
    }
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        // Na succesvolle bewerking, redirect naar dashboard met een statusmelding
        header('Location: admin_dashboard.php?status=updated');
        exit();
    } else {
        echo "Fout bij bijwerken van docent.";
    }
}

// Verkrijg docent informatie
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Docent Bewerken</title>
</head>
<body>
    <h1>Docent Bewerken</h1>
    <form action="edit_teacher.php" method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($teacher['id']); ?>">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
        <br>
        <label for="password">Wachtwoord (laat leeg om niet te wijzigen):</label>
        <input type="password" id="password" name="password">
        <br>
        <input type="submit" value="Bijwerken">
    </form>
    <p><a href="admin_dashboard.php">Terug naar dashboard</a></p>
</body>
</html>
