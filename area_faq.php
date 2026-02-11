<?php
include "db.php";
session_start();

// Recupero dati utente se presenti
$username = $_SESSION['utente'] ?? null;
$nome_completo = $_SESSION['nome'] ?? "";
$email = $_SESSION['email'] ?? "";

// --- 1. GESTIONE INSERIMENTO ---
if (isset($_POST['invia_commento']) && !empty(trim($_POST['testo_commento'])) && $username) {
    $testo = trim($_POST['testo_commento']);
    // Recupero il voto (stelle) dal form
    $stelle = (!empty($_POST['voto_stelle'])) ? intval($_POST['voto_stelle']) : null;
    
    // MODIFICA STRETTAEMNTE NECESSARIA: Aggiunto public. e colonna stelle
    $sql_ins = "INSERT INTO public.faq (username, recensione, data_recensione, stelle) 
                VALUES ($1, $2, CURRENT_TIMESTAMP, $3)";
    
    pg_query_params($db, $sql_ins, array($username, $testo, $stelle));
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- 2. RECUPERO DATI SIDEBAR ---
$res_sidebar = null;
if ($username) {
    $sql_select = "SELECT * FROM prenotazioni WHERE username_utente = $1 AND data_ora >= CURRENT_TIMESTAMP ORDER BY data_ora ASC";
    $res_sidebar = pg_query_params($db, $sql_select, array($username));
}

// --- 3. RECUPERO FAQ ---
// MODIFICA STRETTAEMNTE NECESSARIA: Aggiunto public.
$sql_faq = "SELECT username, recensione, data_recensione, stelle FROM public.faq ORDER BY data_recensione DESC";
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
        <?php if ($username): ?>
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
    <?php if ($username): ?>
        <p class="titolo-sidebar">MENU</p>
        <ul>
            <li><a href="mainpage.php">Torna alla Home</a></li>
            <?php pg_result_seek($risultatoGiochi, 0);
            while ($r = pg_fetch_assoc($risultatoGiochi)): ?>
                <li><a href="dettaglio_gioco.php?gioco=<?= urlencode($r['nome_gioco']) ?>"><?= htmlspecialchars($r['nome_gioco']) ?></a></li>
            <?php endwhile; ?>
            <li><a href="area_faq.php">Area Food e Recensioni</a></li>
        </ul>
    <?php else: ?>
        <p class="titolo-sidebar">SERVIZI OFFERTI</p>
        <ul>
            <li><a href="mainpage.php#presentazione">Home</a></li>
            <?php pg_result_seek($risultatoGiochi, 0);
            while ($rowSide = pg_fetch_assoc($risultatoGiochi)):
                $anchorId = str_replace(' ', '-', $rowSide['nome_gioco']); ?>
                <li><a href="mainpage.php#<?= $anchorId ?>"><?= htmlspecialchars($rowSide['nome_gioco']) ?></a></li>
            <?php endwhile; ?>
            <li><a href="mainpage.php#area-food">Area Food e Recensioni</a></li>
        </ul>
    <?php endif; ?>
</aside>

<main style="flex: 1; padding: 20px;">
    <h1 style="color: #ff00ff; text-align: center; text-transform: uppercase;">Area Food & Community</h1>
    
    <div class="faq-area">
        <h2 style="color: #00b7ff; margin-bottom: 20px;">Lascia una Recensione</h2>
        
        <?php if ($username): ?>
            <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <span class="label-voto">Seleziona il tuo voto:</span>
                <div class="rating-stars">
                    <input type="radio" id="star5" name="voto_stelle" value="5" required /><label for="star5">★</label>
                    <input type="radio" id="star4" name="voto_stelle" value="4" /><label for="star4">★</label>
                    <input type="radio" id="star3" name="voto_stelle" value="3" /><label for="star3">★</label>
                    <input type="radio" id="star2" name="voto_stelle" value="2" /><label for="star2">★</label>
                    <input type="radio" id="star1" name="voto_stelle" value="1" /><label for="star1">★</label>
                </div>

                <textarea name="testo_commento" rows="3" 
                            style="width:100%; margin-bottom:15px; background:#111; color:#fff; border:1px solid #444; padding:10px; border-radius:5px;" 
                            placeholder="Cosa ne pensi del nostro cibo o del servizio?" required></textarea>
                
                <button type="submit" name="invia_commento" 
                        style="background:#ff00ff; color:white; border:none; padding:12px 25px; cursor:pointer; font-weight:bold; border-radius:5px; width: 100%;">
                    PUBBLICA RECENSIONE
                </button>
            </form>
        <?php else: ?>
            <p style="text-align:center; padding: 20px; background: #222; border-radius: 5px;">
                <a href="login.php" style="color: #ff00ff; font-weight: bold; text-decoration: none;">Accedi</a> per poter scrivere una recensione.
            </p>
        <?php endif; ?>

        <div style="margin-top: 40px;">
            <h3 style="border-bottom: 2px solid #ff00ff; padding-bottom: 10px; color: #eee;">Recensioni degli Utenti</h3>
            <?php if ($risultato_faq && pg_num_rows($risultato_faq) > 0): ?>
                <?php while ($f = pg_fetch_assoc($risultato_faq)): ?>
                    <div class="commento-singolo">
                        <div class="commento-meta">
                            <strong style="color: #00b7ff; font-size: 1.1em;">@<?= htmlspecialchars($f['username']) ?></strong>
                            <span style="margin-left: 10px;">il <?= date('d/m/Y H:i', strtotime($f['data_recensione'])) ?></span>
                            
                            <?php if (!empty($f['stelle'])): ?>
                                <span style="color: #ffcc00; margin-left: 15px; font-size: 1.2em;">
                                    <?= str_repeat('★', $f['stelle']) ?><span style="color: #333;"><?= str_repeat('★', 5 - $f['stelle']) ?></span>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="commento-testo">"<?= htmlspecialchars($f['recensione']) ?>"</div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #888; text-align: center; margin-top: 20px;">Ancora nessuna recensione. Sii il primo!</p>
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
                    $data_f = date('d M, H:i', strtotime($item['data_ora'])); ?>
                    <li>
                        <strong style="color: #00b7ff; text-transform: uppercase;"><?= htmlspecialchars($item['nome_gioco']) ?></strong><br>
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
        <?php if ($username): ?>
            <p>User: <?= htmlspecialchars($username) ?></p>
            <p>Email: <?= htmlspecialchars($email) ?></p>
        <?php else: ?>
            <p style="font-style: italic; line-height: 1.6;">
                Non sei ancora dei nostri?<br>
                Registrati subito per accedere<br>
                a tutti i servizi del club<br>
                e gestire le tue prenotazioni!
            </p>
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