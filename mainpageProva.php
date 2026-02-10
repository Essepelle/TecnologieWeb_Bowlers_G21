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
    <link rel="stylesheet" href="mainpageProva.css">
    <link rel="icon" type="icon" href="resources/logo.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="header">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center;">
                <img src="resources/logo.png" style="width: 50px; margin-right: 15px;">
                <h1 style="margin: 0;">BOWLERS - THE BOWLER CLUB</h1>
            </div>
            
            <div class="user-info">
                <?php if (isset($_SESSION['utente'])): ?>
                    <p>Ciao, <strong><?= htmlspecialchars($_SESSION['nome']) ?></strong></p>
                    <a href="prenotazioni.php">Le mie Prenotazioni</a> | 
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.html" style="color: white; text-decoration: none;">
                        <i class="fa-solid fa-user"></i> LOGIN
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="link">
            <p id="sottolineato">SERVIZI OFFERTI</p>
            <ul style="font-size: 100%; list-style: none; padding: 0;">
                <li><a href="#presentazione">Presentazione</a></li>
                <li><a href="#giochi">Carambola</a></li>
                <li><a href="#giochi">Bowling</a></li>
                <li><a href="#giochi">Torneo Carte</a></li>
                <li><a href="#giochi">Laser Game</a></li>
                <li><a href="#footer-box">Area FAQ</a></li>
            </ul>
        </div>

        <div class="main">
            <h2 id="presentazione">I Nostri Servizi</h2>
            <p>Benvenuto, questi sono i servizi che offriamo presso il nostro club:</p>

            <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
                <?php while ($row = pg_fetch_assoc($risultatoGiochi)): ?>
                    <div class="card-gioco">
                        <img src="<?= htmlspecialchars($row['immagine']) ?>" style="width: 100%; height: 120px; object-fit: cover; border-radius: 4px;">
                        <h3 style="font-size: 1.1em; margin: 10px 0;"><?= htmlspecialchars($row['nome_gioco']) ?></h3>
                        
                        <form method="POST">
                            <input type="hidden" name="nome_gioco" value="<?= htmlspecialchars($row['nome_gioco']) ?>">
                            <button type="submit" name="add_to_cart" style="cursor: pointer; padding: 5px 15px;">
                                Prenota
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="prenotazioni">
            <p id="sottolineato">PRENOTAZIONI ATTIVE</p>
            <div id="carrello-box" style="font-size: 90%;">
                <?php if (!empty($_SESSION['carrello'])): ?>
                    <ul style="padding-left: 20px;">
                        <?php foreach ($_SESSION['carrello'] as $gioco): ?>
                            <li>
                                <?= htmlspecialchars($gioco) ?> 
                                <br><a href="prenota_dettaglio.php?gioco=<?= urlencode($gioco) ?>" style="font-size: 0.8em;">Configura</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <button onclick="window.location.href='checkout.php'" style="width: 100%; margin-top: 10px;">Conferma Tutto</button>
                <?php else: ?>
                    <p>Nessuna prenotazione</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>© 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21</p>
    </div>
</body>
</html>