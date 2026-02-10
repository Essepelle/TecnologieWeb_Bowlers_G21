<?php
include "db.php";
session_start();

// Controllo se l'utente è loggato (usiamo 'utente' come chiave basata sui tuoi screenshot)
if (!isset($_SESSION['utente'])) {
    header("Location: login.html");
    exit();
}

if (isset($_POST['conferma_prenotazione'])) {
    $username = $_SESSION['utente']; 
    $nome_gioco = $_POST['nome_gioco'];
    $data = $_POST['data_prenotazione'];
    $ora = $_POST['ora_prenotazione'];
    
    // Creazione del timestamp compatibile con PostgreSQL (YYYY-MM-DD HH:MM)
    $data_ora = $data . ' ' . $ora;

    // Inizializzazione variabili per evitare errori di indice se i campi non sono nel POST
    $pista = !empty($_POST['numero_pista']) ? $_POST['numero_pista'] : null;
    $tavolo = !empty($_POST['numero_tavolo']) ? $_POST['numero_tavolo'] : null;
    $persone = !empty($_POST['numero_persone']) ? $_POST['numero_persone'] : null;
    $torneo = null;

    // Gestione del booleano per la partecipazione al torneo
    if (isset($_POST['partecipa_torneo'])) {
        $torneo = ($_POST['partecipa_torneo'] === 'si') ? 'true' : 'false';
    }

    // Query SQL basata sulla struttura della tua tabella 'prenotazioni'
    $sql = "INSERT INTO prenotazioni (username_utente, nome_gioco, data_ora, numero_pista, numero_tavolo, numero_persone, partecipazione_torneo) 
            VALUES ($1, $2, $3, $4, $5, $6, $7)";

    $params = array(
        $username, 
        $nome_gioco, 
        $data_ora, 
        $pista, 
        $tavolo, 
        $persone, 
        $torneo
    );

    $result = pg_query_params($db, $sql, $params);

    if ($result) {
        // Se la prenotazione ha successo, la rimuoviamo dal carrello visivo se presente
        if (isset($_SESSION['carrello']) && ($key = array_search($nome_gioco, $_SESSION['carrello'])) !== false) {
            unset($_SESSION['carrello'][$key]);
        }
        // Reindirizzamento con messaggio di successo
        header("Location: mainpage.php?prenotazione=ok");
    } else {
        // In caso di errore del database
        echo "Si è verificato un errore durante l'inserimento: " . pg_last_error($db);
    }
} else {
    // Se si tenta di accedere al file senza inviare il form
    header("Location: mainpage.php");
}
?>