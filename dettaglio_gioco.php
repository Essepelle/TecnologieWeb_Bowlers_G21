<?php
include "db.php";
session_start();
$username = $_SESSION['utente'];
$email = $_SESSION['email'];

// 1. Recupero il nome del gioco dalla URL
$nomeGioco = $_GET['gioco'] ?? '';
if (empty($nomeGioco)) {
    header("Location: mainpage.php");
    exit();
}

// 2. Recupero dati immagine dal DB
$sql = "SELECT immagine FROM giochi WHERE nome_gioco = $1";
$risultato = pg_query_params($db, $sql, array($nomeGioco));
$gioco = pg_fetch_assoc($risultato);

if (!$gioco) {
    die("Gioco non trovato.");
}

// 3. Lettura descrizione estesa
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
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css"> <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/it.js"></script>
    <script src="data_orario.js"></script>
</head>

<body>

<header>
    <div class="site" onclick="window.location.href='mainpage.php'">
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
        <li><a href="mainpage.php">Torna alla Home</a></li>
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
            <h1><?= htmlspecialchars($nomeGioco) ?></h1>
            
            <form method="POST" action="gestisci_prenotazione.php" onsubmit="if(!document.getElementById('ora_prenotazione_valore').value){alert('Seleziona un orario!'); return false;}">
                <input type="hidden" name="nome_gioco" value="<?= htmlspecialchars($nomeGioco) ?>">
                
                <label>Scegli il Giorno:</label><br>
                <input type="text" id="data_prenotazione" name="data_prenotazione" placeholder="Scegli data.." readonly required><br><br>

                <label>Scegli l'Orario:</label>
                <span class="label-istruzioni">(Clicca su un orario disponibile)</span>
                <div id="orari-bottoni-container" class="orari-container">
                    <p style="color: #555;">Seleziona prima una data valida.</p>
                </div>
                
                <input type="hidden" id="ora_prenotazione_valore" name="ora_prenotazione" required>

                <?php if ($nomeGioco == 'Biliardo'): ?>
                    <?php endif; ?>

                <br>
                <button type="submit" name="conferma_prenotazione" class="btn-submit">CONFERMA</button>
            </form>
        </div>
    </div>
</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">PRENOTAZIONI ATTIVE</p>
    <div id="carrello-box">
        <?php if (pg_num_rows($res_sidebar) > 0): ?>
            <ul style="list-style: none; padding: 0;">
                <?php while ($item = pg_fetch_assoc($res_sidebar)): 
                    $data_f = date('d M, H:i', strtotime($item['data_ora']));
                ?>
                    <li style="margin-bottom: 15px; border-bottom: 1px solid #ff00ff40; padding-bottom: 5px;">
                        <strong style="color: #00b7ff; text-transform: uppercase;"><?= htmlspecialchars($item['nome_gioco']) ?></strong><br>
                        <span style="color: #ccc;"><?= $data_f ?></span>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p style="color: #999; font-style: italic;">Nessuna prenotazione trovata</p>
        <?php endif; ?>
    </div>
    <p class="titolo-sidebar" style="margin-top: 30px;">ACCOUNT</p>
    <div style="font-size: 0.9em; color: #ccc;">
        <p style="overflow-wrap: break-word;">Username: <?= htmlspecialchars($username) ?></p>
        <p style="overflow-wrap: break-word;">Email: <?= htmlspecialchars($email) ?></p>
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
                <?php endif; ?>
                const url = new URL(window.location);
                url.searchParams.delete('res');
                window.history.replaceState({}, document.title, url);
            }, 100);
        });
    </script>
<?php endif; ?>

<script>
        // Avvia la logica passando il nome del gioco da PHP a JS
        document.addEventListener('DOMContentLoaded', function() {
            initPrenotazione("<?= $nomeGioco ?>");
        });
</script>

</body>
</html>