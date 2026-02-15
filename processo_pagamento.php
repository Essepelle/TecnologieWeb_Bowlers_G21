<?php
include "db.php";
session_start();

if (isset($_SESSION['pending_reservation']) && isset($_POST['esegui_pagamento'])) {
    
    $numero_carta = $_POST['numero_carta'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    $scadenza = $_POST['scadenza'] ?? ''; // Recuperiamo la scadenza

    // --- NUOVO CONTROLLO DATA SCADENZA ---
    $is_expired = false;
    if (preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $scadenza, $matches)) {
        $mese = (int)$matches[1];
        $anno = (int)$matches[2];
        $mese_attuale = (int)date('m');
        $anno_attuale = (int)date('y');

        if ($anno < $anno_attuale || ($anno == $anno_attuale && $mese < $mese_attuale)) {
            $is_expired = true;
        }
    } else {
        $is_expired = true; // Formato non valido
    }
    // -------------------------------------

    // Aggiorniamo la validazione esistente
    if (strlen($numero_carta) !== 16 || strlen($cvv) !== 3 || $is_expired) {
        header("Location: dettaglio_gioco.php?gioco=Torneo di Carte&res=payment_failed");
        exit();
    }

    // --- INIZIO MODIFICA ---
    $dati_sessione = $_SESSION['pending_reservation'];

    // Ricostruiamo l'array per avere sempre 6 elementi (se mancano, mette NULL)
    $params = array(
        $dati_sessione[0],                                    // Username
        $dati_sessione[1],                                    // Nome Gioco
        $dati_sessione[2],                                    // Data Ora
        isset($dati_sessione[3]) ? $dati_sessione[3] : NULL,  // Pista
        isset($dati_sessione[4]) ? $dati_sessione[4] : NULL,  // Tavolo
        isset($dati_sessione[5]) ? $dati_sessione[5] : NULL   // Persone
    );
    // ... resto del codice per l'inserimento nel DB ...
    $sql_insert = "INSERT INTO prenotazioni (username_utente, nome_gioco, data_ora, numero_pista, numero_tavolo, numero_persone) 
                   VALUES ($1, $2, $3, $4, $5, $6)";
    
    $res_insert = pg_query_params($db, $sql_insert, $params);

    if ($res_insert) {
        unset($_SESSION['pending_reservation']);
        header("Location: dettaglio_gioco.php?gioco=Torneo di Carte&res=success");
    } else {
        echo "Errore tecnico durante il salvataggio: " . pg_last_error($db);
    }
} else {
    header("Location: index.php");
    exit();
}
?>