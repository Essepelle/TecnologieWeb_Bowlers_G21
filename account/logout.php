<?php
session_start();

$_SESSION = array();        // Avvio la sessione esistente per poterla gestire

// Se la sessione usa i cookie, elimino quello salvato nel browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    // Imposto il cookie con una scadenza passata per cancellarlo
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Distruggo definitivamente la sessione sul server
session_destroy();

// Reindirizzo l'utente alla pagina principale
header("Location: ../index.php");
exit();
?>