<?php
session_start();
// Rimuove tutte le variabili di sessione
$_SESSION = array();

// Se si desidera distruggere completamente la sessione, si cancella anche il cookie di sessione
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Distrugge la sessione
session_destroy();

// Reindirizza alla home o al login
header("Location: mainpage.php");
exit();
?>