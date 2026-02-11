<?php
include "db.php";
session_start();

if (!isset($_SESSION['utente'])) {
    header("Location: login.html");
    exit();
}

if (isset($_POST['conferma_prenotazione'])) {
    $username = $_SESSION['utente'];
    $nome_gioco = $_POST['nome_gioco'];
    $data = $_POST['data_prenotazione'];
    $ora = $_POST['ora_prenotazione'];
    $data_ora = $data . ' ' . $ora; // Formato TIMESTAMP per il DB

    $numero_tavolo = $_POST['numero_tavolo'] ?? null;
    $numero_pista = $_POST['numero_pista'] ?? null;
    $numero_persone = $_POST['numero_persone'] ?? null;
    $partecipa_torneo = $_POST['partecipa_torneo'] ?? null;

    $sql = "INSERT INTO prenotazioni (username_utente, nome_gioco, data_ora, numero_tavolo, numero_pista, numero_persone, partecipazione_torneo) 
            VALUES ($1, $2, $3, $4, $5, $6, $7)
            ON CONFLICT DO NOTHING";// per non far inserire prenotazioni allo stesso gioco nella stessa ora
    
    $params = array($username, $nome_gioco, $data_ora, $numero_tavolo, $numero_pista, $numero_persone, $partecipazione_torneo);
    $result = pg_query_params($db, $sql, $params);

    if ($result) {
        header("Location: mainpage.php?msg=success");
        exit();
    } else {
        echo "Errore: " . pg_last_error($db);
    }
} else {
    header("Location: mainpage.php");
}
?>