<?php
include "db.php";
session_start();

$nomeGioco = $_GET['gioco'] ?? '';
if (empty($nomeGioco)) { header("Location: mainpage.php"); exit(); }

// Query immagine
$sql = "SELECT immagine FROM giochi WHERE nome_gioco = $1";
$res = pg_query_params($db, $sql, array($nomeGioco));
$gioco = pg_fetch_assoc($res);

// Caricamento descrizione estesa dalla cartella descrizioni/
$nomeFileDescr = "descrizioni/descr_" . strtolower(str_replace(' ', '_', $nomeGioco)) . ".txt";
$descrizioneEstesa = file_exists($nomeFileDescr) ? file_get_contents($nomeFileDescr) : "Dettagli non disponibili.";

// Sidebar sinistra
$risultatoGiochi = pg_query($db, "SELECT nome_gioco FROM giochi ORDER BY nome_gioco;");
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($nomeGioco) ?> - Dettaglio</title>
    <link rel="stylesheet" href="mainpage.css">
</head>
<body>

<header>
    <div class="site" onclick="window.location.href='mainpage.php'">
        <img src="resources/logo.png" class="logo">
        <h1>The Bowler Club</h1>
    </div>
    <div class="user">
        <h2><?= isset($_SESSION['nome']) ? "Ciao " . htmlspecialchars($_SESSION['nome']) : "Effettua il Login" ?></h2>
    </div>
</header>

<aside class="sidebar-left">
    <p class="titolo-sidebar">SERVIZI OFFERTI</p>
    <ul>
        <?php while ($r = pg_fetch_assoc($risultatoGiochi)): ?>
            <li><a href="mainpage.php#<?= str_replace(' ', '-', $r['nome_gioco']) ?>"><?= htmlspecialchars($r['nome_gioco']) ?></a></li>
        <?php endwhile; ?>
    </ul>
</aside>

<main>
    <div class="card-gioco-odd">
        <img src="<?= htmlspecialchars($gioco['immagine']) ?>" alt="<?= htmlspecialchars($nomeGioco) ?>">
        
        <div>
            <h1><?= htmlspecialchars($nomeGioco) ?></h1>
            <p><?= nl2br(htmlspecialchars($descrizioneEstesa)) ?></p>
            
            <form method="POST" action="mainpage.php">
                <input type="hidden" name="nome_gioco" value="<?= htmlspecialchars($nomeGioco) ?>">
                <button type="submit" name="add_to_cart">Conferma Prenotazione</button>
            </form>
            <button type="button" onclick="window.location.href='mainpage.php'" style="background: #444;">Torna Indietro</button>
        </div>
    </div>
</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">PRENOTAZIONI ATTIVE</p>
    <div id="carrello-box">
        <?php if (!empty($_SESSION['carrello'])): ?>
            <ul>
                <?php foreach ($_SESSION['carrello'] as $item): ?>
                    <li><?= htmlspecialchars($item) ?></li>
                <?php endforeach; ?>
            </ul>
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