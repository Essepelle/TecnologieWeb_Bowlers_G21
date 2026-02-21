<?php
include "../db.php";
session_start();

// recupero dati sticky form
$old_data = $_SESSION['old_post'] ?? [];
unset($_SESSION['old_post']); // Pulisce la sessione dopo aver preso i dati


// recupero errori/successi lato server
$error_prenotazione = $_SESSION['error_prenotazione'] ?? '';
$success_prenotazione = $_SESSION['success_prenotazione'] ?? '';
unset($_SESSION['error_prenotazione'], $_SESSION['success_prenotazione']);

// Dati utente per sidebar e logica prenotazione
$username = $_SESSION['utente'];
$email = $_SESSION['email'];

// 1. Recupero il nome del gioco dalla URL
$nomeGioco = $_GET['gioco'] ?? '';
if (empty($nomeGioco)) {
    header("Location: ../index.php");
    exit();
}

// 2. Recupero dati immagine dal DB
$sql = "SELECT immagine FROM giochi WHERE nome_gioco = $1";
$risultato = pg_query_params($db, $sql, array($nomeGioco));
$gioco = pg_fetch_assoc($risultato);

if (!$gioco) {
    die("Gioco non trovato.");
}

// 3. Lettura descrizione estesa e ridotta
$nomeFilePres = "resources/descrizioni/pres_" . strtolower(str_replace(' ', '_', $nomeGioco)) . ".txt";
$descrizioneRidotta = file_exists("../" . $nomeFilePres) ? file_get_contents("../" . $nomeFilePres) : "Dettagli non disponibili.";

$nomeFileDescr = "resources/descrizioni/descr_" . strtolower(str_replace(' ', '_', $nomeGioco)) . ".txt";
$descrizioneEstesa = file_exists("../" . $nomeFileDescr) ? file_get_contents("../" . $nomeFileDescr) : "Dettagli non disponibili.";

// 4. Recupero i giochi per la sidebar sinistra
$sqlGiochi = "SELECT nome_gioco FROM giochi ORDER BY nome_gioco;";
$risultatoGiochi = pg_query($db, $sqlGiochi);

// 5. Recupero le prenotazioni per la sidebar di destra
$sql_sidebar = "SELECT nome_gioco, data_ora FROM prenotazioni 
                WHERE username_utente = $1 
                AND data_ora >= CURRENT_TIMESTAMP
                ORDER BY data_ora ASC LIMIT 5";
$res_sidebar = pg_query_params($db, $sql_sidebar, array($username));

$oggi = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($nomeGioco) ?> - Prenota</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../mainpage.css">
    <link rel="stylesheet" href="dettaglio_gioco.css">
    <link rel="icon" type="icon" href="../resources/logo.png"/>
    
    <script src="data_orario.js"></script>
</head>

<body>

<header>
    <div class="site" onclick="window.location.href='../index.php'">
        <img src="../resources/logo.png" class="logo" alt="Logo">
        <h1>The Bowler Club</h1>
    </div>
    <div class="user">
        <?php if (isset($_SESSION['utente'])): ?>
            <div class="dropdown-container">
                <h2 style="cursor: pointer;">
                    <?php 
                        // Dividio il nome completo in un array usando lo spazio come separatore, 
                        // in modo da prendere solo il nome
                        $parti_nome = explode(' ', trim($_SESSION['nome'])); 
                        $primo_nome = $parti_nome[0]; 
                    ?>
                    Ciao <?= htmlspecialchars($primo_nome) ?>
                </h2>                
                <div class="logged-menu">
                    <a href="../account/prenotazioni.php">Le mie Prenotazioni</a>
                    <a href="../account/logout.php">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <h2 onclick="window.location.href='../login/login.php'" style="cursor:pointer;">Effettua il Login</h2>
        <?php endif; ?>
    </div>
</header>

<aside class="sidebar-left">
    <p class="titolo-sidebar">SERVIZI OFFERTI</p>
    <ul>
        <li><a href="../index.php">Torna alla Home</a></li>
        <?php while ($r = pg_fetch_assoc($risultatoGiochi)): ?>
            <li>
                <a href="dettaglio_gioco.php?gioco=<?= urlencode($r['nome_gioco']) ?>">
                    <?= htmlspecialchars($r['nome_gioco']) ?>
                </a>
            </li>
        <?php endwhile; ?>
        <li><a href="../area_faq/area_faq.php">Area Food e Recensioni</a></li>
    </ul>
</aside>

<main>
    <div class="card-gioco-odd">
        <img src="../<?= htmlspecialchars($gioco['immagine']) ?>">
        <div>
            <h1 id="titolo"><?= htmlspecialchars($nomeGioco) ?></h1>
            
            <div class="box-descrizione">
                <?php echo nl2br(htmlspecialchars($descrizioneRidotta)); ?> <!-- nl2br è un metodo che mantiene gli spazi dettati dentro la stringa originale -->
            </div>

            <div class="box-regole">
                <p class="titolo-sidebar">REGOLE:</p>
                <?php echo nl2br(htmlspecialchars($descrizioneEstesa)); ?> <!-- nl2br è un metodo che mantiene gli spazi dettati dentro la stringa originale -->
            </div>
        </div> 
    </div> 

    <div class="card-prenotazione">
        <h2 style="border-bottom: 2px solid #00b7ff; padding-bottom: 10px; margin-top: unset; margin-bottom: 20px;">Prenota la tua partita</h2>

        <!-- div per gli errori lato server -->
        <?php if (!empty($success_prenotazione)): ?>
            <div class="success-box">
                <i class="fa-solid fa-circle-check"></i> 
                <?= htmlspecialchars($success_prenotazione) ?>
                <i class="fa-solid fa-circle-check"></i> 
            </div>
        <?php endif; ?>

        <?php if (!empty($error_prenotazione)): ?>
            <div class="error-box">
                <i class="fa-solid fa-triangle-exclamation"></i> 
                <?= htmlspecialchars($error_prenotazione) ?>
                <i class="fa-solid fa-triangle-exclamation"></i> 
            </div>
        <?php endif; ?>

        <form method="POST" action="gestisci_prenotazione.php" 
            onsubmit="return validaPrenotazione('<?= htmlspecialchars($nomeGioco) ?>');"><!-- Quando si fa la submit si avviano i controlli in JS per gli errori lato client -->
            <input type="hidden" name="nome_gioco" value="<?= htmlspecialchars($nomeGioco) ?>">
            
            <div class="prenotazione-layout">
                
                <div class="col-data-ora">
                    <label for="data_prenotazione">SCEGLI IL GIORNO:</label>
                    <input type="date" id="data_prenotazione" name="data_prenotazione" placeholder="Scegli data.." required
                    value="<?= htmlspecialchars($old_data['data_prenotazione'] ?? '') ?>">

                    <label>SCEGLI L'ORARIO:</label>
                    <span class="label-istruzioni">(Clicca su un orario disponibile)</span>
    
                        <div id="orari-bottoni-container" class="orari-container">
                            <p>Seleziona prima una data valida!</p>
                        </div>
    
                    <input type="hidden" id="ora_prenotazione_valore" name="ora_prenotazione" 
                    value="<?= htmlspecialchars($old_data['ora_prenotazione'] ?? '') ?>">
                </div>

                <div class="col-opzioni">
                    <div class="box-input-specifici">
                        
                        <?php if ($nomeGioco == 'Bowling'): ?>
                            <label>Seleziona Pista (1-24):</label>
                            <div class="selector-grid">
                                <?php for ($i = 1; $i <= 24; $i++): ?>
                                    <input type="radio" id="pista_<?= $i ?>" name="numero_pista" value="<?= $i ?>" 
                                    <?= (isset($old_data['numero_pista']) && $old_data['numero_pista'] == $i) ? 'checked' : '' ?>><!-- Aggiunta logica per STICKY FORM -->
                                    <label for="pista_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>

                        <?php elseif ($nomeGioco == 'Biliardo'): ?>
                            <label>Seleziona Tavolo (1-6):</label>
                            <div class="selector-grid">
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <input type="radio" id="tavolo_<?= $i ?>" name="numero_tavolo" value="<?= $i ?>" 
                                    <?= (isset($old_data['numero_tavolo']) && $old_data['numero_tavolo'] == $i) ? 'checked' : '' ?>><!-- Aggiunta logica per STICKY FORM -->
                                    <label for="tavolo_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>

                        <?php elseif ($nomeGioco == 'Laser Game'): ?>
                            <label>Numero Persone (Max 10):</label>
                            <div class="selector-grid">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <input type="radio" id="persone_<?= $i ?>" name="numero_persone" value="<?= $i ?>" 
                                    <?= (isset($old_data['numero_persone']) && $old_data['numero_persone'] == $i) ? 'checked' : '' ?>><!-- Aggiunta logica per STICKY FORM -->
                                    <label for="persone_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        
                        <?php elseif ($nomeGioco == 'Torneo di Carte'): ?>
                        <label>INFO DATA e ORARIO</label>
                        <p style="font-size: 0.9em; color: #bbb; margin-top: 10px;">
                        I Tornei di Carte si svolgono ogni <b>Mercoledì</b> e <b>Venerdì</b> 
                        del mese alle <b>21:00</b>.
                        </p>
                        <br><br>
                        <label><u>ATTENZIONE</u> : Pagamento Bancario</label>
                        <p style="font-size: 0.9em; color: #bbb; margin-top: 10px;">
                        Per questo servizio è richiesto il pagamento anticipato di <b>€ 5.00</b>.
                        Cliccando su "Conferma Prenotazione", sarai reindirizzato 
                        in modo sicuro al nostro portale di pagamento bancario 
                        per completare l'operazione.
                        </p>
                        <?php endif; ?>

                    </div>

                    <button type="submit" name="conferma_prenotazione" class="btn-submit" style="width: 100%; margin-top: 20px; font-size: 1.2em;">CONFERMA PRENOTAZIONE</button>
                </div>
            </div>
        </form>
    </div>
</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">ACCOUNT</p>
    <div>
        <p> <p id="title-blue"> Username </p> <?= htmlspecialchars($username) ?></p>
        <p > <p id="title-blue"> Email </p> <?= htmlspecialchars($email) ?></p>
        <p class="titolo-sidebar" style="margin-top: 40px;">PRENOTAZIONI ATTIVE</p>
    
        <div id="carrello-box">
            <?php if ($res_sidebar && pg_num_rows($res_sidebar) > 0): ?>
                <p style="color:#888;">Solo le più recenti:</p>
                <ul>
                    <?php while ($item = pg_fetch_assoc($res_sidebar)): 
                        $data_f = date('d M, H:i', strtotime($item['data_ora'])); //Formatto la data
                    ?>
                        <li id="dettaglio-pren-gioco">
                            <strong id="title-blue">
                                <?= htmlspecialchars($item['nome_gioco']) ?>
                            </strong><br>
                            <span style="color: #ccc;"><?= $data_f ?></span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p style="color: #999; font-style: italic;">Nessuna prenotazione trovata</p>
            <?php endif; ?>
        </div>
    
    </div>
</aside>

<footer>
    © 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inizializza
        initPrenotazione("<?= $nomeGioco ?>");

        // 2. Se PHP ha rimesso la data (sticky), scatena l'evento per mostrare gli orari
        const dataInput = document.getElementById('data_prenotazione');
        if (dataInput.value) {
            dataInput.dispatchEvent(new Event('change'));
        }
    });

    // Funzione di validazione lato client prima dell'invio del form
</script>

<script>
function validaPrenotazione(nomeGioco) {
    // Pulisce eventuali spazi vuoti dal nome per sicurezza
    var giocoClean = nomeGioco.trim();

    // --- 1. CONTROLLO ORARIO ---
    // (Vale per tutti tranne Torneo di Carte)
    const orarioInput = document.getElementById('ora_prenotazione_valore');
    
    if (!orarioInput.value) {
        alert("Per favore, seleziona un orario cliccando sui bottoni!");
        return false; 
    }

    // --- 2. CONTROLLO RISORSE (Basato su cosa c'è nella pagina) ---

    // CASO BOWLING: Cerco se esistono input con name="numero_pista"
    if (document.querySelector('input[name="numero_pista"]')) {
        // Se esistono, controllo se ALMENO UNO è selezionato (:checked)
        if (!document.querySelector('input[name="numero_pista"]:checked')) {
            alert("Devi selezionare il numero della Pista!");
            return false;
        }
    }

    // CASO BILIARDO: Cerco se esistono input con name="numero_tavolo"
    if (document.querySelector('input[name="numero_tavolo"]')) {
        if (!document.querySelector('input[name="numero_tavolo"]:checked')) {
            alert("Devi selezionare il numero del Tavolo!");
            return false;
        }
    }

    // CASO LASER GAME: Cerco se esistono input con name="numero_persone"
    if (document.querySelector('input[name="numero_persone"]')) {
        if (!document.querySelector('input[name="numero_persone"]:checked')) {
            alert("Devi selezionare il numero di persone!");
            return false;
        }
    }

    // Se tutto è ok
    return true;
}
</script>
</body>
</html>