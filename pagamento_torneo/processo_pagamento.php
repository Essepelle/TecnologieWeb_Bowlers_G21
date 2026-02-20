<?php
include "../db.php";
session_start();

// Controllo se ci sono dati in sessione e se il form è stato inviato
if (isset($_SESSION['pending_reservation']) && isset($_POST['esegui_pagamento'])) {
    
    // --- 1. VALIDAZIONE CARTA DI CREDITO ---
    $numero_carta = $_POST['numero_carta'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    $scadenza = $_POST['scadenza'] ?? '';

    $is_expired = false;
    // Controllo formato MM/AA e validità temporale
    if (preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $scadenza, $matches)) {
        $mese = (int)$matches[1];
        $anno = (int)$matches[2];
        $mese_attuale = (int)date('m');
        $anno_attuale = (int)date('y'); // Anno a 2 cifre

        if ($anno < $anno_attuale || ($anno == $anno_attuale && $mese < $mese_attuale)) {
            $is_expired = true;
        }
    } else {
        $is_expired = true; 
    }

    // Se la carta non è valida, rimanda indietro con errore
    if (strlen($numero_carta) !== 16 || strlen($cvv) !== 3 || $is_expired) {
        $_SESSION['error_payment'] = "I dati della carta non sono validi o la carta risulta scaduta.";
        $_SESSION['old_payment'] = $_POST; // per lo sticky form
        header("Location: pagamento_torneo.php");
        exit();
    }

    // --- 2. PREPARAZIONE DATI PER IL DATABASE ---
    $dati_sessione = $_SESSION['pending_reservation'];

    // Ricostruiamo l'array per avere sempre ESATTAMENTE 6 parametri.
    // Questo è il passaggio chiave che ha fatto funzionare il debug.
    $params = array(
        $dati_sessione[0],                                    // Username
        $dati_sessione[1],                                    // Nome Gioco
        $dati_sessione[2],                                    // Data Ora
        isset($dati_sessione[3]) ? $dati_sessione[3] : NULL,  // Pista (o NULL)
        isset($dati_sessione[4]) ? $dati_sessione[4] : NULL,  // Tavolo (o NULL)
        isset($dati_sessione[5]) ? $dati_sessione[5] : NULL   // Persone (o NULL)
    );

    // --- 3. INSERIMENTO NEL DATABASE ---
    $sql_insert = "INSERT INTO prenotazioni (username_utente, nome_gioco, data_ora, numero_pista, numero_tavolo, numero_persone) 
                   VALUES ($1, $2, $3, $4, $5, $6)";
    
    $res_insert = pg_query_params($db, $sql_insert, $params);

    if ($res_insert) {
        // ... (lascia intatto il blocco del successo) ...
        unset($_SESSION['pending_reservation']);
        header("Location: ../dettaglio_gioco/dettaglio_gioco.php?gioco=" . urlencode($params[1]) . "&res=success");
        exit();
    } else {
        // Errore tecnico del database
        $_SESSION['error_payment'] = "Errore di Sistema nel salvataggio. Riprova.";
        header("Location: pagamento_torneo.php");
        exit();
    }

} else {
    // Accesso non autorizzato (es. accesso diretto via URL)
    header("Location: ../index.php");
    exit();
}
?>