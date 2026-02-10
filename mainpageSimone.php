<?php
include "db.php"; 
session_start();

/* 1) AGGIUNTA GIOCO AL CARRELLO */
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
    <link rel="stylesheet" href="mainpageSimone.css">
    <link rel="icon" type="icon" href="resources/logo.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
<header>
    <div class="site" onclick="window.location.href='mainpageSimone.php'">
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
                    <a onclick="<?php session_write_close()?>" href="mainpageSimone.php">Logout</a>
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
                <li><a href="#giochi">Carambola</a></li>
                <li><a href="#giochi">Bowling</a></li>
                <li><a href="#giochi">Torneo Carte</a></li>
                <li><a href="#giochi">Laser Game</a></li>
                <li><a href="#footer-box">Area FAQ</a></li>
            </ul>
        </aside>

        <main>
            <h2 id="presentazione">I Nostri Servizi</h2>
            <p>Benvenuto, questi sono i servizi che offriamo presso il nostro club:</p>

            <div class="grid-giochi">
                <?php
                $i = 0;
                while ($row = pg_fetch_assoc($risultatoGiochi)):
                    $i++;
                    $classe = ($i % 2 === 0) ? 'card-gioco-even' : 'card-gioco-odd';
                ?>
                    <hr/>
                    <div class="<?= $classe ?>">
                        <img src="<?= htmlspecialchars($row['immagine']) ?>" alt="<?= htmlspecialchars($row['nome_gioco']) ?>">
                        <h1><?= htmlspecialchars($row['nome_gioco']) ?></h1>
                        
                        <form method="POST">
                            <input type="hidden" name="nome_gioco" value="<?= htmlspecialchars($row['nome_gioco']) ?>">
                            <button type="submit" name="add_to_cart">
                                Prenota
                            </button>
                        </form>
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