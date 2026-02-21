<?php
include "../db.php";
session_start();

// Controllo sicurezza: l'utente deve essere loggato
if (!isset($_SESSION['utente']) || !isset($_POST['conferma_eliminazione'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['utente'];

// 1. Elimina le prenotazioni associate all'utente (opzionale se hai ON DELETE CASCADE nel DB)
$sql_del_pren = "DELETE FROM prenotazioni WHERE username_utente = $1";
pg_query_params($db, $sql_del_pren, array($username));

// 2. Elimina l'utente dalla tabella utenti
$sql_del_user = "DELETE FROM utenti WHERE username = $1";
$result = pg_query_params($db, $sql_del_user, array($username));

if ($result) {
    // 3. Distruggi la sessione e reindirizza alla pagina di login o home
    session_destroy();
    header("Location: ../index.php?msg=account_eliminato");
    exit();
} else {
    echo "Errore durante l'eliminazione dell'account: " . pg_last_error($db);
}
?>