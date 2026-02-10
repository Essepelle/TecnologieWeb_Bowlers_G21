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

   // ... dopo l'inserimento nel database con successo ...
    if ($result) {
        // Rimuoviamo il gioco dal carrello temporaneo della sessione (se lo usi ancora)
        if (isset($_SESSION['carrello'])) {
            unset($_SESSION['carrello']); 
        }
        // Reindirizziamo alla home o alle prenotazioni
        header("Location: prenotazioni.php?msg=success");
        exit();
    }
} else {
    // Se si tenta di accedere al file senza inviare il form
    header("Location: mainpage.php");
}
?>