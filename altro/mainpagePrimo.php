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

/* 2) RECUPERA TUTTI I GIOCHI E LE IMMAGINI DAL DB */
// Selezioniamo nome e immagine come da tuo screenshot
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
    <title>The Bowler Club - Giochi</title>
    <link rel="stylesheet" href="mainpage.css">
    <link rel="icon" type="icon" href="resources/logo.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<header>
    <div class="site" onclick="window.location.href='mainpage.php'">
        <img src="resources/logo.png" class="logo" alt="Logo">
        <h1>The Bowler Club</h1>
    </div>
    <div class="user">
        <?php if (isset($_SESSION['utente'])): ?>
            <h2>
                Ciao <?= htmlspecialchars($_SESSION['nome']) ?>
                <i class="fa-solid fa-user" style="margin-left: 10px;"></i>
            </h2>
            <div class="logged-menu">
                <a href="prenotazioni.php">Le mie Prenotazioni</a> | 
                <a href="logout.php">Logout</a>
            </div>
        <?php else: ?>
            <h2 onclick="window.location.href='login.html'" style="cursor:pointer;">Effettua il Login</h2>
        <?php endif; ?>
    </div>
</header>

<main>
    <section class="vetrina">
        <h2>I Nostri Giochi</h2>
        <div class="grid">
            <?php while ($row = pg_fetch_assoc($risultatoGiochi)): ?>
                <div class="prodotto">
                    <img src="<?= htmlspecialchars($row['immagine']) ?>" alt="<?= htmlspecialchars($row['nome_gioco']) ?>">
                    <h3><?= htmlspecialchars($row['nome_gioco']) ?></h3>
                    
                    <form method="POST">
                        <input type="hidden" name="nome_gioco" value="<?= htmlspecialchars($row['nome_gioco']) ?>">
                        <button type="submit" name="add_to_cart" class="button">Seleziona</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <hr>

    <section class="carrello">
        <h2>I tuoi Giochi Selezionati</h2>
        <ul>
            <?php if (!empty($_SESSION['carrello'])): ?>
                <?php foreach ($_SESSION['carrello'] as $giocoSelezionato): ?>
                    <li>
                        <i class="fa-solid fa-bowling-ball"></i> 
                        <strong><?= htmlspecialchars($giocoSelezionato) ?></strong>
                        <a href="prenota_dettaglio.php?gioco=<?= urlencode($giocoSelezionato) ?>" class="link-prenota">Configura Prenotazione</a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Non hai ancora selezionato nessun gioco.</li>
            <?php endif; ?>
        </ul>
    </section>
</main>

<footer>
    <p>© 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21</p>
</footer>

</body>
</html>