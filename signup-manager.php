<?php
include 'db.php';
session_start();

// Verifichiamo la ricezione di tutti i dati dal form HTML
if (isset($_POST['username'], $_POST['password'], $_POST['nome'], $_POST['email'])) {
    
    $nome_completo = $_POST['nome'];
    $email         = $_POST['email'];
    $username      = $_POST['username'];
    $password      = $_POST['password'];
    $conferma      = $_POST['conferma_password'];

    $_SESSION['old_data'] = [
        'nome'=> $nome_completo,
        'email'=> $email,
        'username'=> $username
    ];
    
    $_SESSION['form_target'] = 'signup';

    
    // 1. Controllo corrispondenza password
    if ($password !== $conferma) {
        $_SESSION['error_signup'] = "Le password non coincidono.";
        header("Location: login.php");
        exit;
    }

    // 2. Controllo se l'utente esiste già
    if (user_exists($username, $db)) {
        $_SESSION["error_signup"] = "L'username '$username' è già in uso.";
        header("Location: login.php");
        exit;
    // 3. Controllo se l'email esiste già
    } elseif (email_exists($email, $db)) {
        $_SESSION["error_signup"] = "L'email '$email' è già in uso.";
        header("Location: login.php");
        exit;
    } else {
        // 4. Registrazione nuovo utente
        $hash_password = password_hash($password, PASSWORD_DEFAULT);

        if (register_user($username, $nome_completo, $email, $hash_password, $db)) {
            unset($_SESSION['old_data']);
            unset($_SESSION['form_target']);
            $_SESSION['success'] = "Registrazione completata con successo.";
            header("Location: login.php");
            exit;
        } else {
            $_SESSION['error_signup'] = "Errore di Sistema nel database.";
            header("Location: login.php");
            exit;
        }
    }
} else {
    header("Location: login.php");
    exit;
}

/**
 * Verifica l'esistenza dello username (PK)
 */
function user_exists($user, $db) {
    // Nota: PostgreSQL è case-sensitive, assicurati che la tabella si chiami 'utenti'
    $sql = "SELECT username FROM utenti WHERE username = $1";
    $ret = pg_query_params($db, $sql, array($user));
    if ($ret && pg_num_rows($ret) > 0) return true;
    return false;
}

function email_exists($email, $db) {
    $sql = "SELECT email FROM utenti WHERE email = $1";
    $ret = pg_query_params($db, $sql, array($email));
    if ($ret && pg_num_rows($ret) > 0) return true;
    return false;
}

/**
 * Inserisce i dati usando i nomi colonne esatti del tuo screenshot:
 * username, nome_completo, email, pass
 */
function register_user($username, $nome, $email, $pwd, $db) {
    // Query adeguata ai nomi colonne: nome_completo e pass
    $sql = "INSERT INTO utenti (username, nome_completo, email, pass) VALUES ($1, $2, $3, $4)";
    $ret = pg_query_params($db, $sql, array($username, $nome, $email, $pwd));
    return (bool)$ret;
}
?>