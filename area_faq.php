<?php
include "db.php";
session_start();

$username = $_SESSION['utente'] ?? null;
$nome_completo = $_SESSION['nome'] ?? "";
$email = $_SESSION['email'] ?? "";

// --- 1. LOGICA INSERIMENTO ---
if (isset($_POST['invia_commento']) && !empty(trim($_POST['testo_commento'])) && $username) {
    $testo = trim($_POST['testo_commento']);
    $stelle = (!empty($_POST['voto_stelle'])) ? intval($_POST['voto_stelle']) : null;
    $sql_ins = "INSERT INTO public.faq (username, recensione, data_recensione, stelle) VALUES ($1, $2, CURRENT_TIMESTAMP, $3)";
    $result = pg_query_params($db, $sql_ins, array($username, $testo, $stelle));
    if ($result) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        die("Errore: " . pg_last_error($db));
    }
}

// --- 2. LOGICA SIDEBAR ---
$res_sidebar = null;
if ($username) {
    $sql_select = "SELECT nome_gioco, data_ora FROM prenotazioni 
                    WHERE username_utente = $1 
                    AND data_ora >= CURRENT_TIMESTAMP
                    ORDER BY data_ora ASC LIMIT 5";
    $res_sidebar = pg_query_params($db, $sql_select, array($username));
}

// --- 3. LOGICA FAQ ---
$filtro_stelle = isset($_GET['filtro_stelle']) ? intval($_GET['filtro_stelle']) : null;

if ($filtro_stelle && $filtro_stelle >= 1 && $filtro_stelle <= 5) {
    // Se c'è un filtro per stelle, ordiniamo per data (più recenti prima)
    $sql_faq = "SELECT username, recensione, data_recensione, stelle 
                FROM public.faq 
                WHERE stelle = $1 
                ORDER BY data_recensione DESC"; 
    $risultato_faq = pg_query_params($db, $sql_faq, array($filtro_stelle));
} else {
    // SELEZIONATO "TUTTE": Ordiniamo solo per data di recensione decrescente
    $sql_faq = "SELECT username, recensione, data_recensione, stelle 
                FROM public.faq 
                ORDER BY data_recensione DESC";
    $risultato_faq = pg_query($db, $sql_faq);
}
$risultatoGiochi = pg_query($db, "SELECT nome_gioco FROM giochi ORDER BY nome_gioco;");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Area Food & FAQ - The Bowler Club</title>
    <link rel="stylesheet" href="mainpage.css">
    <link rel="stylesheet" href="area_faq.css">
</head>
<body>

<header>
    <div class="site clickable" onclick="window.location.href='mainpage.php'">
        <img src="resources/logo.png" class="logo" alt="Logo">
        <h1>The Bowler Club</h1>
    </div>
    <div class="user">
        <?php if ($username): ?>
            <div class="dropdown-container">
                <h2 class="clickable">Ciao <?= htmlspecialchars($_SESSION['nome']) ?></h2>
                <div class="logged-menu">
                    <a href="prenotazioni.php">Le mie Prenotazioni</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <h2 onclick="window.location.href='login.php'" class="clickable">Effettua il Login</h2>
        <?php endif; ?>
    </div>
</header>

<aside class="sidebar-left">
    <p class="titolo-sidebar">SERVIZI OFFERTI</p>
    <ul>
        <li><a href="mainpage.php"><?= $username ? 'Torna alla Home' : 'Home' ?></a></li>
        <?php pg_result_seek($risultatoGiochi, 0);
        while ($r = pg_fetch_assoc($risultatoGiochi)): ?>
            <li><a href="dettaglio_gioco.php?gioco=<?= urlencode($r['nome_gioco']) ?>"><?= htmlspecialchars($r['nome_gioco']) ?></a></li>
        <?php endwhile; ?>
        <li><a href="area_faq.php">Area Food e Recensioni</a></li>
    </ul>
</aside>

<main class="main-content">
    <h1 id="titolo" class="page-title">Area Food & Community</h1>
    
    <div class="food-section">
        <div class="photo-menu">
        <div class="comments-scroll-box">
        <div class="menu">
            <strong>Burger & Main Course</strong>
            <a id="article">Strike Burger</a><a id="price">11.00€</a>
            <a id="article">Chicken King</a><a id="price">10.50€</a>
            <a id="article">Veggie Spare</a><a id="price">12.00€</a>
            <a id="article">Hot Dog Classico</a><a id="price">7.50€</a>
            <a id="article">Pinsa Margherita</a><a id="price">9.00€</a>
            <strong>Bibite & Birre</strong>
            <a id="article">Acqua Naturale/Frizzante</a><a id="price">1.00€</a>
            <a id="article">Soft drinks</a><a id="price">2.50€</a>
            <a id="article">Birra alla spina 20cl</a><a id="price">3.50€</a>
            <a id="article">Birra alla spina 40cl</a><a id="price">5.00€</a>
            <a id="article">Birre Speciali in bottiglia</a><a id="price">5.00€</a>
            <a id="article">Monster/Red Bull</a><a id="price">3.50€</a>
            <strong>Cocktail & Bowling Nigh</strong>
            <a id="article">Spritz (Aperol/Campari)</a><a id="price">6.50€</a>
            <a id="article">Gin Tonic/Lemon</a><a id="price">6.50€</a>
            <a id="article">Vodka Tonic/Lemon</a><a id="price">6.00€</a>
            <a id="article">Mojito</a><a id="price">7.00€</a>
            <a id="article">Long Island Iced Tea</a><a id="price">7.00€</a>
            <a id="article">Analcolico alla Frutta</a><a id="price">5.00€</a>
            <strong>Dolci e Caffè</strong>
            <a id="article">Waffle con Nutella e panna</a><a id="price">5.50€</a>
            <a id="article">Coppa Gelato (3 gusti)</a><a id="price">4.00€</a>
            <a id="article">Caffè Espresso</a><a id="price">1.00€</a>
        </div>
        </div>
        <img src="img/area_food.jpg">
        </div>
        <p>
            Appendi la stecca al chiodo e posa la carabina: è tempo di rifocillarsi nella nostra Area Food, il quartier generale del gusto e del relax! Tra un burger succulento e una pizza da campioni, potrai commentare i tuoi successi (o le tue clamorose sviste) in un'atmosfera vibrante e amichevole. Che tu sia qui per una cena di squadra o per uno snack veloce tra una sfida e l'altra, troverai sempre il carburante giusto per tornare in pista più forte di prima. Ricorda: lo strike più importante della serata è quello che farai seduto a tavola! Unisciti a noi, brinda con gli amici e goditi il lato più saporito del divertimento.
        </p>
    </div>

    <div class="faq-area">
        <h2 class="faq-section-title">Lascia una Recensione</h2>
        
        <?php if ($username): ?>
            <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <span class="label-voto">Seleziona il tuo voto:</span>
                <div class="rating-stars">
                    <?php for($i=5; $i>=1; $i--): ?>
                        <input type="radio" id="star<?= $i ?>" name="voto_stelle" value="<?= $i ?>" <?= $i==5?'required':'' ?> /><label for="star<?= $i ?>">★</label>
                    <?php endfor; ?>
                </div>
            <textarea 
                name="testo_commento" 
                class="review-textarea" 
                placeholder="Cosa ne pensi?" 
                required
                oninput='this.style.height = ""; this.style.height = this.scrollHeight + "px"'
            ></textarea>
                <button type="submit" name="invia_commento" class="btn-submit-review">PUBBLICA RECENSIONE</button>
            </form>
        <?php else: ?>
            <p class="login-alert"><a href="login.php">Accedi</a> per poter scrivere una recensione.</p>
        <?php endif; ?>

        <div class="reviews-container">
            <div class="reviews-header">
                <h3 id="sezione-recensioni">Recensioni degli Utenti</h3>
                <div class="filter-wrapper">
                    <span class="filter-label">Filtra per:</span>
                    <a href="?#sezione-recensioni" class="filter-link <?= !$filtro_stelle ? 'filter-all-active' : 'filter-all-inactive' ?>">Tutte</a>
                    <?php for($i=5; $i>=1; $i--): ?>
                        <a href="?filtro_stelle=<?= $i ?>#sezione-recensioni" class="filter-link <?= ($filtro_stelle == $i) ? 'filter-active' : 'filter-inactive' ?>"><?= $i ?> ★</a>
                    <?php endfor; ?>
                </div>
            </div>
            <div id="box-commenti-scroll" class="comments-scroll-box">
                <?php if ($risultato_faq && pg_num_rows($risultato_faq) > 0): ?>
                    <?php while ($f = pg_fetch_assoc($risultato_faq)): ?>
                        <div class="comment-card">
                            <div class="comment-meta">
                                <strong class="comment-author">@<?= htmlspecialchars($f['username']) ?></strong>
                                <span class="comment-date">il <?= date('d/m/Y', strtotime($f['data_recensione'])) ?></span>
                                <?php if (!empty($f['stelle'])): ?>
                                    <span class="comment-stars"><?= str_repeat('★', $f['stelle']) ?><span class="comment-stars-empty"><?= str_repeat('★', 5 - $f['stelle']) ?></span></span>
                                <?php endif; ?>
                            </div>
                            <div class="comment-text">"<?= htmlspecialchars($f['recensione']) ?>"</div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-reviews">Nessuna recensione trovata.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">ACCOUNT</p>
    <div style="font-size: 0.9em; color: #ccc;">
        <?php if (isset($_SESSION['utente'])): ?>
            <p style="overflow-wrap: break-word;"> <p id="title-blue">Username</p> <?= htmlspecialchars($username) ?></p>
            <p style="overflow-wrap: break-word;"> <p id="title-blue">Email</p> <?= htmlspecialchars($email) ?></p>
            
            <p class="titolo-sidebar"style="margin-top: 40px;">PRENOTAZIONI ATTIVE</p>
            <div id="carrello-box">
                <?php if ($res_sidebar && pg_num_rows($res_sidebar) > 0): ?>
                    <p style="color:#888;">Solo le più recenti:</p>
                    <ul style="list-style: none; padding: 0;">
                        <?php while ($item = pg_fetch_assoc($res_sidebar)): 
                            // Formattiamo la data per renderla più bella (es. 12 Feb, 21:00)
                            $data_f = date('d M, H:i', strtotime($item['data_ora']));
                        ?>
                            <li style="margin-bottom: 15px; border-bottom: 1px solid #ff00ff40; padding-bottom: 5px;">
                                <strong id="title-blue">
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