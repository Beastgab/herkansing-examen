<?php
require_once '../includes/user.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new User($myDb);
    try {
        $email = htmlspecialchars($_POST['email']);
        $pass = htmlspecialchars($_POST['password']);
        $userExist = $user->login($email);

        if ($userExist) {
            $passVerify = password_verify($pass, $userExist['password']);
            if ($passVerify) {
                session_start();
                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id'] = $userExist['id'];
                $_SESSION['username'] = $userExist['email'];
                $_SESSION['role'] = $userExist['roles']; // Zorg ervoor dat je de rol van de gebruiker opslaat in de sessie

                // Redirect naar de juiste dashboard op basis van de rol
                switch ($userExist['roles']) {
                    case 'admin':
                        header("Location: admin_dashboard.php");
                        break;
                    case 'scheduler':
                        header("Location: scheduler_dashboard.php");
                        break;
                    case 'teacher':
                        header("Location: teacher_dashboard.php");
                        break;
                    case 'student':
                        header("Location: student_dashboard.php");
                        break;
                    default:
                        var_dump( $_SESSION);
                        die();
                        header("Location: login.php"); // Redirect naar login als rol onbekend
                        break;
                }
                exit();
            } else {
                echo "Incorrect username or password";
            } 
        } else {
            echo "Incorrect username or password";
        }
    } catch (\Exception $e) {
        echo 'Error: ' . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form method="POST">
        <input type="text" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>
    <p><a href="register.php">Registreren</a></p>
</body>
</html>
