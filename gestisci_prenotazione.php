<?php
include "db.php";
session_start();

if (!isset($_SESSION['utente'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['conferma_prenotazione'])) {
    $username = $_SESSION['utente'];
    $nomeGioco = $_POST['nome_gioco'];
    $data = $_POST['data_prenotazione'];
    $ora = $_POST['ora_prenotazione'];

    // Controllo sicurezza Carte: solo Mercoledì (3) e Venerdì (5) alle 21:00
    if ($nomeGioco === 'Carte') {
        $giorno = date('N', strtotime($data));
        if ($giorno != 3 && $giorno != 5) {
            die("Giorno non valido per Carte.");
        }
        $ora = "21:00"; 
    }

    $dataOraFinale = $data . " " . $ora;

    // Controllo 1: utente ha già una prenotazione a quest'ora (qualsiasi gioco)
    $sql_check_orario = "SELECT * FROM prenotazioni WHERE username_utente = $1 AND data_ora = $2";
    $res_check_orario = pg_query_params($db, $sql_check_orario, array($username, $dataOraFinale));

    if (pg_num_rows($res_check_orario) > 0) {
        header("Location: dettaglio_gioco.php?gioco=" . urlencode($nomeGioco) . "&res=orario_occupato");
        exit();
    }

    // Recupero dati dal form
    $n_tavolo = $_POST['numero_tavolo'] ?? null;
    $n_pista  = $_POST['numero_pista'] ?? null;
    $n_persone = $_POST['numero_persone'] ?? null;
    $torneo   = $_POST['partecipa_torneo'] ?? null;

    // NUOVO CONTROLLO: Verifica disponibilità fisica della risorsa (Tavolo o Pista)
    // Questo previene l'errore "unica_prenotazione_tavolo" nel database
    $sql_check_disponibilita = "SELECT * FROM prenotazioni 
                                WHERE nome_gioco = $1 
                                AND data_ora = $2 
                                AND (
                                    (numero_tavolo IS NOT NULL AND numero_tavolo = $3) OR 
                                    (numero_pista IS NOT NULL AND numero_pista = $4)
                                )";
    
    $res_dispo = pg_query_params($db, $sql_check_disponibilita, array($nomeGioco, $dataOraFinale, $n_tavolo, $n_pista));

    if (pg_num_rows($res_dispo) > 0) {
        // Se il tavolo o la pista sono già occupati da qualcun altro
        header("Location: dettaglio_gioco.php?gioco=" . urlencode($nomeGioco) . "&res=risorsa_occupata");
        exit();
    }

    // Conversione valore torneo per colonna boolean
    $torneo_bool = null;
    if ($torneo === 'si') {
        $torneo_bool = 'true';
    } elseif ($torneo === 'no') {
        $torneo_bool = 'false';
    }

    // QUERY CORRETTA: senza la virgola di troppo
    $sql_insert = "INSERT INTO prenotazioni (
        username_utente, 
        nome_gioco, 
        data_ora, 
        numero_pista, 
        numero_tavolo, 
        numero_persone      
    ) VALUES ($1, $2, $3, $4, $5, $6)";
    
    $params = array(
        $username,      // $1
        $nomeGioco,     // $2
        $dataOraFinale, // $3
        $n_pista,       // $4
        $n_tavolo,      // $5
        $n_persone      // $6
    );
    
    // Logica Torneo Carte
    if ($nomeGioco === 'Carte') {
        $_SESSION['pending_reservation'] = $params;
        header("Location: pagamento_torneo.php");
        exit();
    }
    
    $res_insert = pg_query_params($db, $sql_insert, $params);

    if ($res_insert) {
        header("Location: dettaglio_gioco.php?gioco=" . urlencode($nomeGioco) . "&res=success");
    } else {
        echo "Errore DB: " . pg_last_error($db);
    }
} else {
    header("Location: mainpage.php");
    exit();
}
?>