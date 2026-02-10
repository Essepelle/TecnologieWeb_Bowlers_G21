<?php
include "db.php";
session_start();

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

// 3. Lettura descrizione estesa dalla cartella descrizioni/
$nomeFileDescr = "descrizioni/descr_" . strtolower(str_replace(' ', '_', $nomeGioco)) . ".txt";
$descrizioneEstesa = file_exists($nomeFileDescr) ? file_get_contents($nomeFileDescr) : "Dettagli non disponibili.";

// 4. Recupero i giochi per la sidebar sinistra
$sqlGiochi = "SELECT nome_gioco FROM giochi ORDER BY nome_gioco;";
$risultatoGiochi = pg_query($db, $sqlGiochi);

// Variabili per controllo data e ora attuale
$oggi = date('Y-m-d');
$ora_attuale = (int)date('H');
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($nomeGioco) ?> - Prenota</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="mainpage.css">
    <link rel="icon" type="icon" href="resources/logo.png"/>
    <script>
        // Funzione JS per filtrare le ore in base alla data selezionata
        function aggiornaOre() {
            const dateInput = document.getElementById('data_prenotazione');
            const hourSelect = document.getElementById('ora_prenotazione');
            const oggi = "<?= $oggi ?>";
            const oraCorrente = <?= $ora_attuale ?>;
            
            hourSelect.innerHTML = "";
            let oraInizio = 10; // Orario apertura club

            if (dateInput.value === oggi) {
                oraInizio = oraCorrente + 1; // Solo ore future rispetto a ora attuale
                if (oraInizio < 10) oraInizio = 10;
            }

            if (oraInizio > 24) {
                const opt = document.createElement('option');
                opt.text = "Esaurito per oggi";
                opt.disabled = true;
                hourSelect.add(opt);
            } else {
                for (let h = oraInizio; h <= 24; h++) {
                    const opt = document.createElement('option');
                    let val = h < 10 ? "0" + h + ":00" : h + ":00";
                    opt.value = val;
                    opt.text = val;
                    hourSelect.add(opt);
                }
            }
        }
    </script>
</head>

<body onload="aggiornaOre()">
<header>
    <div class="site" onclick="window.location.href='mainpage.php'">
        <img src="resources/logo.png" class="logo" alt="Logo">
        <h1>The Bowler Club</h1>
    </div>
    <div class="user">
        <?php if (isset($_SESSION['utente'])): ?>
            <div class="dropdown-container">
                <h2 style="cursor: pointer;">
                    Ciao <?= htmlspecialchars($_SESSION['nome']) ?>
                </h2>
                <div class="logged-menu">
                    <a href="prenotazioni.php">Le mie Prenotazioni</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <h2 onclick="window.location.href='login.html'" style="cursor:pointer;">Effettua il Login</h2>
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
    </ul>
</aside>

<main>
    <div class="card-gioco-odd">
        <img src="<?= htmlspecialchars($gioco['immagine']) ?>" alt="<?= htmlspecialchars($nomeGioco) ?>">
        
        <div>
            <h1><?= htmlspecialchars($nomeGioco) ?></h1>
            <p><?= nl2br(htmlspecialchars($descrizioneEstesa)) ?></p>
            
            <form method="POST" action="gestisci_prenotazione.php">
                <input type="hidden" name="nome_gioco" value="<?= htmlspecialchars($nomeGioco) ?>">
                
                <label>Seleziona Giorno:</label><br>
                <input type="date" id="data_prenotazione" name="data_prenotazione" 
                       min="<?= $oggi ?>" value="<?= $oggi ?>" onchange="aggiornaOre()" required><br><br>

                <label>Orario (Solo ore intere):</label><br>
                <select id="ora_prenotazione" name="ora_prenotazione" required></select><br><br>

                <?php if ($nomeGioco == 'Biliardo'): ?>
                    <label>Seleziona Tavolo:</label><br>
                    <select name="numero_tavolo" required>
                        <?php foreach(['01','02','03','04','05','06'] as $t) echo "<option value='$t'>$t</option>"; ?>
                    </select>

                <?php elseif ($nomeGioco == 'Bowling'): ?>
                    <label>Seleziona Pista:</label><br>
                    <select name="numero_pista" required>
                        <?php for($p=1; $p<=24; $p++) echo "<option value='$p'>Pista $p</option>"; ?>
                    </select>

                <?php elseif ($nomeGioco == 'Laser Game'): ?>
                    <label>Numero Persone:</label><br>
                    <input type="number" name="numero_persone" min="1" max="20" required>

                <?php elseif ($nomeGioco == 'Carte'): ?>
                    <label>Partecipazione Torneo:</label><br>
                    <select name="partecipa_torneo">
                        <option value="si">Sì, partecipa al torneo</option>
                        <option value="no">No, tavolo libero</option>
                    </select>
                <?php endif; ?>

                <br><br>
                <button type="submit" name="conferma_prenotazione">Aggiungi alla prenotazione</button>
            </form>
            <br>
            <button type="button" onclick="window.location.href='mainpage.php'" style="background: #444;">Annulla</button>
        </div>
    </div>
</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">PRENOTAZIONI ATTIVE</p>
    <div id="carrello-box">
        <?php if (!empty($_SESSION['carrello'])): ?>
            <ul><?php foreach ($_SESSION['carrello'] as $item) echo "<li>".htmlspecialchars($item)."</li>"; ?></ul>
        <?php else: ?>
            <p>Nessuna prenotazione</p>
        <?php endif; ?>
    </div>
</aside>

<footer>
    <div id="footer-box">
        <p>© 2026 - The Bowler Club</p>
    </div>
</footer>

</body>
</html>