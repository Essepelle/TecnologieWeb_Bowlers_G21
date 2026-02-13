<?php
include "db.php";
session_start();
$username = $_SESSION['utente'];
$email = $_SESSION['email'];

// 1. Recupero il nome del gioco dalla URL
$nomeGioco = $_GET['gioco'] ?? '';
if (empty($nomeGioco)) {
    header("Location: index.php");
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
$nomeFilePres = "descrizioni/pres_" . strtolower(str_replace(' ', '_', $nomeGioco)) . ".txt";
$descrizioneRidotta = file_exists($nomeFilePres) ? file_get_contents($nomeFilePres) : "Dettagli non disponibili.";

$nomeFileDescr = "descrizioni/descr_" . strtolower(str_replace(' ', '_', $nomeGioco)) . ".txt";
$descrizioneEstesa = file_exists($nomeFileDescr) ? file_get_contents($nomeFileDescr) : "Dettagli non disponibili.";

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
    <link rel="stylesheet" href="mainpage.css">
    <link rel="stylesheet" href="dettaglio_gioco.css">
    <link rel="icon" type="icon" href="resources/logo.png"/>
    
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css"> 
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/it.js"></script>-->
    <script src="data_orario.js"></script>
</head>

<body>

<header>
    <div class="site" onclick="window.location.href='index.php'">
        <img src="resources/logo.png" class="logo" alt="Logo">
        <h1>The Bowler Club</h1>
    </div>
    <div class="user">
        <?php if (isset($_SESSION['utente'])): ?>
            <div class="dropdown-container">
                <h2 style="cursor: pointer;">Ciao <?= htmlspecialchars($_SESSION['nome']) ?></h2>
                <div class="logged-menu">
                    <a href="prenotazioni.php">Le mie Prenotazioni</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <h2 onclick="window.location.href='login.php'" style="cursor:pointer;">Effettua il Login</h2>
        <?php endif; ?>
    </div>
</header>

<aside class="sidebar-left">
    <p class="titolo-sidebar">SERVIZI OFFERTI</p>
    <ul>
        <li><a href="index.php">Torna alla Home</a></li>
        <?php while ($r = pg_fetch_assoc($risultatoGiochi)): ?>
            <li>
                <a href="dettaglio_gioco.php?gioco=<?= urlencode($r['nome_gioco']) ?>">
                    <?= htmlspecialchars($r['nome_gioco']) ?>
                </a>
            </li>
        <?php endwhile; ?>
        <li><a href="area_faq.php">Area Food e Recensioni</a></li>
    </ul>
</aside>

<main>
    <div class="card-gioco-odd">
        <img src="<?= htmlspecialchars($gioco['immagine']) ?>">
        <div>
            <h1 id="titolo"><?= htmlspecialchars($nomeGioco) ?></h1>
            
            <div class="box-descrizione">
                <?= nl2br(htmlspecialchars($descrizioneRidotta)) ?>
            </div>

            <div class="box-regole">
                <p class="titolo-sidebar">REGOLE:</p>
                <?= nl2br(htmlspecialchars($descrizioneEstesa)) ?>
            </div>
        </div> 
    </div> 

    <div class="card-prenotazione">
        <h2 style="border-bottom: 2px solid #00b7ff; padding-bottom: 10px; margin-top: unset; margin-bottom: 20px;">Prenota la tua partita</h2>

        <form method="POST" action="gestisci_prenotazione.php" onsubmit="if(!document.getElementById('ora_prenotazione_valore').value){alert('Seleziona un orario!'); return false;}">
            <input type="hidden" name="nome_gioco" value="<?= htmlspecialchars($nomeGioco) ?>">
            
            <div class="prenotazione-layout">
                
<div class="col-data-ora">
    <label for="data_prenotazione">SCEGLI IL GIORNO:</label>
    <input type="date" id="data_prenotazione" name="data_prenotazione" placeholder="Scegli data.." required>

    <label>SCEGLI L'ORARIO:</label>
    <span class="label-istruzioni">(Clicca su un orario disponibile)</span>
    
    <div id="orari-bottoni-container" class="orari-container">
        <p>Seleziona prima una data valida!</p>
    </div>
    
    <input type="hidden" id="ora_prenotazione_valore" name="ora_prenotazione" required>
</div>

                <div class="col-opzioni">
                    <div class="box-input-specifici">
                        
                        <?php if ($nomeGioco == 'Bowling'): ?>
                            <label>Seleziona Pista (1-24):</label>
                            <div class="selector-grid">
                                <?php for ($i = 1; $i <= 24; $i++): ?>
                                    <input type="radio" id="pista_<?= $i ?>" name="numero_pista" value="<?= $i ?>" required>
                                    <label for="pista_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>

                        <?php elseif ($nomeGioco == 'Biliardo'): ?>
                            <label>Seleziona Tavolo (1-6):</label>
                            <div class="selector-grid">
                                <?php for ($i = 1; $i <= 6; $i++): ?>
                                    <input type="radio" id="tavolo_<?= $i ?>" name="numero_tavolo" value="<?= $i ?>" required>
                                    <label for="tavolo_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>

                        <?php elseif ($nomeGioco == 'Laser Game'): ?>
                            <label>Numero Persone (Max 10):</label>
                            <div class="selector-grid">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <input type="radio" id="persone_<?= $i ?>" name="numero_persone" value="<?= $i ?>" required>
                                    <label for="persone_<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        
                        <?php elseif ($nomeGioco == 'Torneo di Carte'): ?>
                        <br><br>
                        <label><u>ATTENZIONE</u> : Pagamento Bancario</label>
                        <p style="font-size: 0.9em; color: #bbb; margin-top: 10px;">
                        Per questo servizio è richiesto il pagamento anticipato.
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
    <div style="font-size: 0.9em; color: #ccc;">
        <p style="overflow-wrap: break-word;"> <p id="title-blue"> Username </p> <?= htmlspecialchars($username) ?></p>
        <p style="overflow-wrap: break-word;"> <p id="title-blue"> Email </p> <?= htmlspecialchars($email) ?></p>
        <p class="titolo-sidebar" style="margin-top: 40px;">PRENOTAZIONI ATTIVE</p>
    
        <div id="carrello-box">
            <?php if (pg_num_rows($res_sidebar) > 0): ?>
                <p style="color:#888;">Solo le più recenti:</p>
                <ul style="list-style: none; padding: 0;">
                    <?php while ($item = pg_fetch_assoc($res_sidebar)): 
                        $data_f = date('d M, H:i', strtotime($item['data_ora']));
                    ?>
                        <li style="margin-bottom: 15px; border-bottom: 1px solid #ff00ff40; padding-bottom: 5px;">
                            <strong id="title-blue"><?= htmlspecialchars($item['nome_gioco']) ?></strong><br>
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
    <div id="footer-box">
        <p>© 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21</p>
    </div>
</footer>

<?php if (isset($_GET['res'])): ?>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                <?php if ($_GET['res'] == 'success'): ?>
                    alert("Prenotazione registrata con successo!");
                <?php elseif ($_GET['res'] == 'duplicate'): ?>
                    alert("Attenzione! Hai già una prenotazione per questa fascia oraria.");
                <?php elseif ($_GET['res'] == 'orario_occupato'): ?>
                    alert("Attenzione! Hai già una prenotazione per questa fascia oraria.");
                <?php elseif ($_GET['res'] == 'risorsa_occupata'): ?>
                    alert("Attenzione! Il tavolo o la pista selezionata è già occupata. Per favore seleziona un'altra risorsa o un altro orario.");
                <?php endif; ?>
                const url = new URL(window.location);
                url.searchParams.delete('res');
                window.history.replaceState({}, document.title, url);
            }, 100);
        });
    </script>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Avvia la logica passando il nome del gioco da PHP a JS
        initPrenotazione("<?= $nomeGioco ?>");
    });
</script>
</body>
</html>