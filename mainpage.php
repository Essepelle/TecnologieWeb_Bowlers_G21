<?php
include "db.php"; 
session_start();

/* 1) AGGIUNTA GIOCO AL CARRELLO (mantenuta per coerenza con il ritorno dal dettaglio) */
if (isset($_POST['add_to_cart'])) {
    $nomeGioco = $_POST['nome_gioco'];
    if (!isset($_SESSION['carrello'])) {
        $_SESSION['carrello'] = [];
    }
    if (!in_array($nomeGioco, $_SESSION['carrello'])) {
        $_SESSION['carrello'][] = $nomeGioco;
    }
}

/* 2) RECUPERA TUTTI I GIOCHI DAL DB */
$sqlGiochi = "SELECT nome_gioco, immagine FROM giochi ORDER BY nome_gioco;";
$risultatoGiochi = pg_query($db, $sqlGiochi);

if (!$risultatoGiochi) {
    die("Errore query giochi: " . pg_last_error($db));
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>The Bowler Club - Main Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="mainpage.css">
    <link rel="icon" type="icon" href="resources/logo.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Aggiunta per scorrimento fluido quando si clicca sulla sidebar */
        html { scroll-behavior: smooth; }
    </style>
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
        <li><a href="#presentazione">Presentazione</a></li>
        <?php 
        // Generazione dinamica dei link della sidebar basata sui giochi nel DB
        pg_result_seek($risultatoGiochi, 0);
        while ($rowSide = pg_fetch_assoc($risultatoGiochi)): 
            $anchorId = str_replace(' ', '-', $rowSide['nome_gioco']);
        ?>
            <li><a href="#<?= $anchorId ?>"><?= htmlspecialchars($rowSide['nome_gioco']) ?></a></li>
        <?php endwhile; ?>
        <li><a href="#areafood">Area Food</a></li>
    </ul>
</aside>

<main>
    <h2 id="presentazione">I Nostri Servizi</h2>
    <p>Benvenuto, questi sono i servizi che offriamo presso il nostro club:</p>

    <div class="grid-giochi">
        <?php
        pg_result_seek($risultatoGiochi, 0);
        $i = 0;
        while ($row = pg_fetch_assoc($risultatoGiochi)):
            $i++;
            $classe = ($i % 2 === 0) ? 'card-gioco-even' : 'card-gioco-odd';
            $anchorId = str_replace(' ', '-', $row['nome_gioco']);
            
            // Puntamento alla cartella descrizioni/
            $nomeFilePres = "descrizioni/pres_" . strtolower(str_replace(' ', '_', $row['nome_gioco'])) . ".txt";
            $presentazione = file_exists($nomeFilePres) ? file_get_contents($nomeFilePres) : "Scopri di più cliccando su prenota.";
        ?>
            <hr/>
            <div id="<?= $anchorId ?>" class="<?= $classe ?>">
                <img src="<?= htmlspecialchars($row['immagine']) ?>" alt="<?= htmlspecialchars($row['nome_gioco']) ?>">
                
                <div class="testo-card">
                    <h1><?= htmlspecialchars($row['nome_gioco']) ?></h1>
                    <p><?= nl2br(htmlspecialchars($presentazione)) ?></p>
                    <button type="button" onclick="window.location.href='dettaglio_gioco.php?gioco=<?= urlencode($row['nome_gioco']) ?>'">
                        Prenota
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">PRENOTAZIONI ATTIVE</p>
    <div id="carrello-box" style="font-size: 90%;">
        <?php if (!empty($_SESSION['carrello'])): ?>
            <ul style="padding-left: 20px;">
                <?php foreach ($_SESSION['carrello'] as $gioco): ?>
                    <li>
                        <?= htmlspecialchars($gioco) ?> 
                        <br><a href="prenota_dettaglio.php?gioco=<?= urlencode($gioco) ?>" style="font-size: 0.8em; color: #ff00ff;">Configura</a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button onclick="window.location.href='checkout.php'" style="width: 100%; margin-top: 10px;">Conferma Tutto</button>
        <?php else: ?>
            <p>Nessuna prenotazione</p>
        <?php endif; ?>
    </div>
</aside>

<footer>
    <div id="footer-box">
        <p>© 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21</p>
    </div>
</footer>
</body>
</html>