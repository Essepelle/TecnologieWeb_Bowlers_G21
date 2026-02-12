<?php
include "db.php";
session_start();

// Verifichiamo che ci sia una sessione attiva e che il form sia stato inviato
if (isset($_SESSION['pending_reservation']) && isset($_POST['esegui_pagamento'])) {
    
    // 1. Recupero dati carta dal POST
    $numero_carta = $_POST['numero_carta'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    // 2. Simulazione Controllo Pagamento (Logica di business)
    // Se la carta non ha 16 cifre, annulliamo tutto
    if (strlen($numero_carta) !== 16 || strlen($cvv) !== 3) {
        header("Location: dettaglio_gioco.php?gioco=Carte&res=payment_failed");
        exit();
    }

    // 3. Se il controllo passa, procediamo con l'inserimento nel DB
    $params = $_SESSION['pending_reservation'];
    
    $sql_insert = "INSERT INTO prenotazioni (username_utente, nome_gioco, data_ora, numero_pista, numero_tavolo, numero_persone, partecipazione_torneo) 
                   VALUES ($1, $2, $3, $4, $5, $6, $7)";
    
    $res_insert = pg_query_params($db, $sql_insert, $params);

    if ($res_insert) {
        unset($_SESSION['pending_reservation']); // Pulizia sessione dopo il successo
        header("Location: dettaglio_gioco.php?gioco=Carte&res=success");
    } else {
        // Se la query fallisce, il database rimane pulito
        echo "Errore tecnico durante il salvataggio: " . pg_last_error($db);
    }
} else {
    // Tentativo di accesso diretto senza passare dal form o sessione scaduta
    header("Location: mainpage.php");
    exit();
}
?>