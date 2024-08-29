<?php
session_start();
require_once '../includes/db.php';

// Controleer of de gebruiker is ingelogd en een docent is
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Verkrijg de informatie van de ingelogde docent
$userStmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$userStmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$userStmt->execute();
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

// Verwerk formulier bij een POST-verzoek
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Wachtwoord mag leeg zijn

    $sql = "UPDATE users SET name = :name, email = :email";
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = :password";
    }
    $sql .= " WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    if (!empty($password)) {
        $stmt->bindParam(':password', $hashed_password);
    }
    $stmt->bindParam(':id', $user_id);

    if ($stmt->execute()) {
        echo "Gegevens bijgewerkt.";
    } else {
        echo "Fout bij bijwerken van gegevens.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bewerk Gegevens</title>
</head>
<body>
    <h1>Bewerk Gegevens</h1>
    <form action="edit_profile.php" method="POST">
        <label for="name">Naam:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <br>
        <label for="password">Nieuw Wachtwoord (laat leeg om niet te wijzigen):</label>
        <input type="password" id="password" name="password">
        <br>
        <input type="submit" value="Bijwerken">
    </form>
    <p><a href="teacher_dashboard.php">Terug naar dashboard</a></p>
</body>
</html>
