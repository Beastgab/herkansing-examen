<?php
session_start();  // Start de sessie

// Controleer of de gebruiker is ingelogd
if (isset($_SESSION['is_logged_in'])) {
    // Vernietig alle sessiegegevens
    session_unset();
    session_destroy();
}

// Redirect naar de inlogpagina of een andere pagina
header("Location: login.php");
exit();
?>
