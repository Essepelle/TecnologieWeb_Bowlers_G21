<?php
include "db.php";
session_start();

if (!isset($_SESSION['utente'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['utente'];
$nome_completo = $_SESSION['nome'];
$email = $_SESSION['email'];

// Gestione Post Commento
if (isset($_POST['invia_commento']) && !empty(trim($_POST['testo_commento']))) {
    $testo = trim($_POST['testo_commento']);
    $sql_ins = "INSERT INTO faq (username, recensioni) VALUES ($1, $2)";
    pg_query_params($db, $sql_ins, array($username, $testo));
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Recupero dati
$sql_select = "SELECT * FROM prenotazioni WHERE username_utente = $1 AND data_ora >= CURRENT_TIMESTAMP ORDER BY data_ora ASC";
$res_sidebar = pg_query_params($db, $sql_select, array($username));

$sql_faq = "SELECT username, recensioni FROM faq ORDER BY username ASC"; 
$risultato_faq = pg_query($db, $sql_faq);

$risultatoGiochi = pg_query($db, "SELECT nome_gioco FROM giochi ORDER BY nome_gioco;");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Area Food & FAQ - The Bowler Club</title>
    <link rel="stylesheet" href="mainpage.css">
</head>
<body>

<header>
    <div class="site" onclick="window.location.href='mainpage.php'">
        <img src="resources/logo.png" class="logo" alt="Logo">
        <h1>The Bowler Club</h1>
    </div>
    <div class="user">
        <h2>Ciao <?= htmlspecialchars($nome_completo) ?></h2>
    </div>
</header>

<aside class="sidebar-left">
    <p class="titolo-sidebar">MENU</p>
    <ul>
        <li><a href="mainpage.php">Torna alla Home</a></li>
        <?php while ($r = pg_fetch_assoc($risultatoGiochi)): ?>
            <li><a href="dettaglio_gioco.php?gioco=<?= urlencode($r['nome_gioco']) ?>"><?= htmlspecialchars($r['nome_gioco']) ?></a></li>
        <?php endwhile; ?>
    </ul>
</aside>

<main>
    <h1 style="color: #ff00ff; text-align: center;">Area Food</h1>
    
    <div class="faq-area">
        <h2 style="color: #00b7ff;">Area FAQ & Community</h2>
        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="faq-form">
            <textarea name="testo_commento" rows="3" placeholder="Scrivi una domanda o una recensione..." required></textarea>
            <button type="submit" name="invia_commento">Pubblica Messaggio</button>
        </form>

        <div style="margin-top: 30px;">
            <h3 style="border-bottom: 1px solid #ff00ff; padding-bottom: 5px;">Commenti Recenti</h3>
            <?php if (pg_num_rows($risultato_faq) > 0): ?>
                <?php while ($f = pg_fetch_assoc($risultato_faq)): ?>
                    <div class="commento-singolo">
                        <span class="commento-user">@<?= htmlspecialchars($f['username']) ?></span>
                        <span class="commento-testo">"<?= htmlspecialchars($f['recensioni']) ?>"</span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #888;">Nessun messaggio presente.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">PRENOTAZIONI ATTIVE</p>
    <div id="carrello-box">
        <?php if ($res_sidebar && pg_num_rows($res_sidebar) > 0): ?>
            <ul>
                <?php while ($item = pg_fetch_assoc($res_sidebar)): 
                    $data_f = date('d M, H:i', strtotime($item['data_ora']));
                ?>
                    <li>
                        <strong style="color: #00b7ff; text-transform: uppercase;">
                            <?= htmlspecialchars($item['nome_gioco']) ?>
                        </strong><br>
                        <span style="color: #ccc; font-size: 0.9em;"><?= $data_f ?></span>
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
        <p>© 2026 - The Bowler Club</p>
    </div>
</footer>

</body>
</html>