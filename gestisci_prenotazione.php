<?php
include "db.php";
session_start();

if (!isset($_SESSION['utente'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['conferma_prenotazione'])) {
    $username = $_SESSION['utente'];
    $nome_gioco = $_POST['nome_gioco'];
    $data = $_POST['data_prenotazione'];
    $ora = $_POST['ora_prenotazione'];
    $data_ora = $data . ' ' . $ora;

    $numero_tavolo = $_POST['numero_tavolo'] ?? null;
    $numero_pista = $_POST['numero_pista'] ?? null;
    $numero_persone = $_POST['numero_persone'] ?? null;
    $partecipa_torneo = ($_POST['partecipa_torneo'] === 'si') ? 'true' : 'false';

    $sql = "INSERT INTO prenotazioni (username_utente, nome_gioco, data_ora, numero_tavolo, numero_pista, numero_persone, partecipazione_torneo) 
            VALUES ($1, $2, $3, $4, $5, $6, $7)
            ON CONFLICT DO NOTHING";
    
    $params = array($username, $nome_gioco, $data_ora, $numero_tavolo, $numero_pista, $numero_persone, $partecipa_torneo);
    $result = pg_query_params($db, $sql, $params);

    if ($result) {
        // Verifichiamo se è stata effettivamente inserita una riga
        if (pg_affected_rows($result) > 0) {
            // Successo: la riga è stata inserita
            header("Location: dettaglio_gioco.php?gioco=" . urlencode($nome_gioco) . "&res=success");
        } else {
            // Conflitto: ON CONFLICT ha bloccato l'inserimento
            header("Location: dettaglio_gioco.php?gioco=" . urlencode($nome_gioco) . "&res=duplicate");
        }
        exit();
    } else {
        echo "Errore tecnico: " . pg_last_error($db);
    }
} else {
    header("Location: mainpage.php");
    exit();
}
?>