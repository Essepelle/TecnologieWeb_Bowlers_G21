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

// 3. Lettura descrizione estesa dalla cartella descrizioni/
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
    function aggiornaOre() {
        const dateInput = document.getElementById('data_prenotazione');
        const hourSelect = document.getElementById('ora_prenotazione');
        
        const oraLocale = new Date();
        const oggiStr = oraLocale.toISOString().split('T')[0];
        const oraCorrente = oraLocale.getHours();
        const minutiCorrenti = oraLocale.getMinutes();
        
        hourSelect.innerHTML = "";
        
        const baseOre = [0, 1, 17, 18, 19, 20, 21, 22, 23];
        
        let listaCompleta = [];
        baseOre.forEach(h => {
            let oraLabel = h < 10 ? "0" + h : h;
            listaCompleta.push({ h: h, m: 0, label: oraLabel + ":00" });
            listaCompleta.push({ h: h, m: 30, label: oraLabel + ":30" });
        });

        let orariDisponibili = listaCompleta;
        
        if (dateInput.value === oggiStr) {
            orariDisponibili = listaCompleta.filter(t => {
                if (t.h > oraCorrente) return true;
                if (t.h === oraCorrente && t.m > minutiCorrenti) return true;
                return false;
            });
        }

        if (orariDisponibili.length === 0) {
            const opt = document.createElement('option');
            opt.text = "Nessun orario disponibile";
            opt.disabled = true;
            hourSelect.add(opt);
        } else {
            orariDisponibili.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.label;
                opt.text = t.label;
                hourSelect.add(opt);
            });
        }
    }
    window.onload = aggiornaOre;
    </script>
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
        <li><a href="area_faq.php">Area Food e Recensioni</a></li>
    </ul>
</aside>

<main>
    <div class="card-gioco-odd">
        <img src="<?= htmlspecialchars($gioco['immagine']) ?>" alt="<?= htmlspecialchars($nomeGioco) ?>">
        
        <div>
            <h1><?= htmlspecialchars($nomeGioco) ?></h1>
            <p><?= nl2br(htmlspecialchars($descrizioneEstesa)) ?></p>
            
            <form method="POST" action="gestisci_prenotazione.php" onsubmit="return confirm('Sei sicuro di voler confermare la prenotazione per <?= htmlspecialchars($nomeGioco) ?>?');">
                <input type="hidden" name="nome_gioco" value="<?= htmlspecialchars($nomeGioco) ?>">
                
                <label>Seleziona Giorno:</label><br>
                <input type="date" id="data_prenotazione" name="data_prenotazione" 
                       min="<?= $oggi ?>" value="<?= $oggi ?>" onchange="aggiornaOre()" required><br><br>

                <label>Orario:</label><br>
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
                    <input type="number" name="numero_persone" min="2" max="10" required>

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
        </div>
    </div>
</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">PRENOTAZIONI ATTIVE</p>
    <div id="carrello-box" style="font-size: 0.9em;">
        <?php if (pg_num_rows($res_sidebar) > 0): ?>
            <ul style="list-style: none; padding: 0;">
                <?php while ($item = pg_fetch_assoc($res_sidebar)): 
                    $data_f = date('d M, H:i', strtotime($item['data_ora']));
                ?>
                    <li style="margin-bottom: 15px; border-bottom: 1px solid #ff00ff40; padding-bottom: 5px;">
                        <strong style="color: #00b7ff; text-transform: uppercase;">
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
        // Usiamo un piccolo ritardo per essere sicuri che la pagina sia visibile
        window.addEventListener('load', function() {
            setTimeout(function() {
                <?php if ($_GET['res'] == 'success'): ?>
                    alert("Prenotazione registrata con successo!");
                <?php elseif ($_GET['res'] == 'duplicate'): ?>
                    alert("Attenzione! Hai già una prenotazione per questo gioco in questa fascia oraria.");
                <?php endif; ?>
                
                // Pulizia URL
                const url = new URL(window.location);
                url.searchParams.delete('res');
                window.history.replaceState({}, document.title, url);
            }, 100);
        });
    </script>
<?php endif; ?>

</body>
</html>