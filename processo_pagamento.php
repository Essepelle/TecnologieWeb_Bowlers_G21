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
        header("Location: dettaglio_gioco.php?gioco=Carte&res=payment_failed");
        exit();
    }

    $params = $_SESSION['pending_reservation'];
    // ... resto del codice per l'inserimento nel DB ...
    $sql_insert = "INSERT INTO prenotazioni (username_utente, nome_gioco, data_ora, numero_pista, numero_tavolo, numero_persone) 
                   VALUES ($1, $2, $3, $4, $5, $6)";
    
    $res_insert = pg_query_params($db, $sql_insert, $params);

    if ($res_insert) {
        unset($_SESSION['pending_reservation']);
        header("Location: dettaglio_gioco.php?gioco=Carte&res=success");
    } else {
        echo "Errore tecnico durante il salvataggio: " . pg_last_error($db);
    }
} else {
    header("Location: index.php");
    exit();
}
?>