<?php
include '../db.php';
session_start();

// Verifichiamo che i dati siano stati inviati dal form
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Salviamo i dati del form nella sessione
    $_SESSION['old_data'] = [
        'username'=> $username,
        'password'=> $password
    ];
    
    // Recuperiamo l'intera riga dell'utente dal DB
    $userRow = get_user($username, $db);

    if (!$userRow) {
        $_SESSION['error_signin'] = "L'username '$username' non esiste.";
        header("Location: login.php");
        exit;
        
    } else {
        // Verifichiamo la password
        if (password_verify($password, $userRow['pass'])) {
            // Login riuscito: inizializziamo la sessione
            $_SESSION['utente'] = $userRow['username'];
            $_SESSION['nome'] = $userRow['nome_completo'];
            $_SESSION['email'] = $userRow['email'];
            unset($_SESSION['old_data']);
            unset($_SESSION['form_target']);
            // Reindirizzamento alla home del sito
            header("Location: ../index.php");
            exit;
        } else {
            $_SESSION['error_signin'] = "Username o password errati.";
            header("Location: login.php");
            exit;
        }
    }
} else {
    header("Location: login.php");
    exit;
}

// Recupera i dati dell'utente basandosi sullo username
function get_user($user, $db)
{
    $sql = "SELECT username, nome_completo, email, pass 
            FROM utenti 
            WHERE username = $1;";

    // Usiamo pg_query_params per semplicità e sicurezza
    $ret = pg_query_params($db, $sql, array($user));

    if (!$ret) {
        return false;
    }

    if ($row = pg_fetch_assoc($ret)) {
        return $row;
    } else {
        return false;
    }
}
?>