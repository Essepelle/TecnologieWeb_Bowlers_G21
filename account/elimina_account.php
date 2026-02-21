<?php
include "../db.php";
session_start();

// Controllo se l'utente è loggato e se ha effettivamente confermato l'eliminazione
if (!isset($_SESSION['utente']) || !isset($_POST['conferma_eliminazione'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['utente'];

// Cancello tutte le prenotazioni collegate al mio account nel database
$sql_del_pren = "DELETE FROM prenotazioni WHERE username_utente = $1";
pg_query_params($db, $sql_del_pren, array($username));

// Procedo con l'eliminazione definitiva del mio profilo dalla tabella utenti
$sql_del_user = "DELETE FROM utenti WHERE username = $1";
$result = pg_query_params($db, $sql_del_user, array($username));

if ($result) {
    // Se l'operazione riesce, distruggo la sessione e torno alla home con un messaggio
    session_destroy();
    header("Location: ../index.php?msg=account_eliminato");
    exit();
} else {
    // Se qualcosa va storto, stampo l'errore restituito dal database
    echo "Errore durante l'eliminazione dell'account: " . pg_last_error($db);
}
?>