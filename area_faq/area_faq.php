<?php
include "../db.php";
session_start();

$username = $_SESSION['utente'] ?? null;
$nome_completo = $_SESSION['nome'] ?? "";
$email = $_SESSION['email'] ?? "";

// --- LOGICA SIDEBAR-DESTRA ---
// Se l'utente è loggato, recupero le sue prime 5 prenotazioni future dal database per mostrarle nel 
// riepilogo della barra laterale destra.
$res_sidebar = null;
if ($username) {
    $sql_select = "SELECT nome_gioco, data_ora FROM prenotazioni 
                    WHERE username_utente = $1 
                    AND data_ora >= CURRENT_TIMESTAMP
                    ORDER BY data_ora ASC LIMIT 5";
    $res_sidebar = pg_query_params($db, $sql_select, array($username));
}

// --- LOGICA RECENSIONE ---
// Gestisco l'invio della recensione verificando che il testo non sia vuoto e l'utente sia loggato, 
// inserendo poi il voto in stelle e il commento nel database PostgreSQL con data corrente.
if (isset($_POST['invia_commento']) && !empty(trim($_POST['testo_commento'])) && $username) {
    $testo = trim($_POST['testo_commento']);
    $stelle = (!empty($_POST['voto_stelle'])) ? intval($_POST['voto_stelle']) : null;
    $sql_ins = "INSERT INTO public.faq (username, recensione, data_recensione, stelle) VALUES ($1, $2, CURRENT_TIMESTAMP, $3)";
    $result = pg_query_params($db, $sql_ins, array($username, $testo, $stelle));
    if ($result) {
        // Se l'inserimento riesce, ricarico la pagina per mostrare il nuovo commento
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        die("Errore: " . pg_last_error($db));
    }
}

// --- LOGICA ORDINE RECENSIONI ---
// Gestisco la visualizzazione delle recensioni applicando, se presente, un filtro per il numero 
// di stelle e ordinando sempre i risultati per mostrare i commenti più recenti.
$filtro_stelle = isset($_GET['filtro_stelle']) ? intval($_GET['filtro_stelle']) : null;

if ($filtro_stelle && $filtro_stelle >= 1 && $filtro_stelle <= 5) {
    $sql_faq = "SELECT username, recensione, data_recensione, stelle 
                FROM public.faq 
                WHERE stelle = $1 
                ORDER BY data_recensione DESC"; 
    $risultato_faq = pg_query_params($db, $sql_faq, array($filtro_stelle));
} else {
    // Se non c'è un filtro per stelle, ordiniamo per data più recenti
    $sql_faq = "SELECT username, recensione, data_recensione, stelle 
                FROM public.faq 
                ORDER BY data_recensione DESC";
    $risultato_faq = pg_query($db, $sql_faq);
}

// Carico infine la lista completa di tutti i giochi per popolare dinamicamente il menu di navigazione laterale.
$risultatoGiochi = pg_query($db, "SELECT nome_gioco FROM giochi ORDER BY nome_gioco;");
?>






<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Area Food & FAQ - The Bowler Club</title>
        <link rel="stylesheet" href="../mainpage.css">
        <link rel="stylesheet" href="area_faq.css">
    </head>

    <body>
        <header>
            <div class="site clickable" onclick="window.location.href='../index.php'"> 
                <img src="../resources/logo.png" class="logo" alt="Logo">
                <h1>The Bowler Club</h1>
            </div>
            <div class="user">
                <?php if ($username): ?>
                    <div class="dropdown-container">
                        <h2 style="cursor: pointer;">
                            <?php 
                                // Dividio il nome completo in un array usando lo spazio come separatore, 
                                // in modo da prendere solo il nome
                                $parti_nome = explode(' ', trim($_SESSION['nome'])); 
                                $primo_nome = $parti_nome[0]; 
                            ?>
                            Ciao <?= htmlspecialchars($primo_nome) ?>
                        </h2>
                        <div class="logged-menu">
                            <a href="../account/prenotazioni.php">Le mie Prenotazioni</a>
                            <a href="../account/logout.php">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <h2 onclick="window.location.href='../login/login.php'" class="clickable">Effettua il Login</h2>
                <?php endif; ?>
            </div>
        </header>

        <aside class="sidebar-left">
            <p class="titolo-sidebar">SERVIZI OFFERTI</p>
            <ul>
                <li><a href="../index.php"><?= $username ? 'Torna alla Home' : 'Home' ?></a></li>
                <?php pg_result_seek($risultatoGiochi, 0);  //riportare il "puntatore" del risultato della query all'inizio (riga 0).
                      while ($r = pg_fetch_assoc($risultatoGiochi)): 
                ?>
                    <li><a href="../dettaglio_gioco/dettaglio_gioco.php?gioco=<?= urlencode($r['nome_gioco']) ?>"><?= htmlspecialchars($r['nome_gioco']) ?></a></li>
                <?php endwhile; ?>
                <li><a href="area_faq.php">Area Food e Recensioni</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h1 id="titolo" class="page-title">Area Food & Recensioni</h1>
            
            <div class="food-section">
                <div class="photo-menu">
                <div class="comments-scroll-box">

                <!-- MENU -->
                <div class="menu">
                    <strong>🍔Burger & Main Course🍔</strong>
                    <a id="article">Strike Burger</a><a id="price">€ 11.00</a>
                    <a id="article">Chicken King</a><a id="price">€ 10.50</a>
                    <a id="article">Veggie Spare</a><a id="price">€ 12.00</a>
                    <a id="article">Hot Dog Classico</a><a id="price">€ 7.50</a>
                    <a id="article">Pinsa Margherita</a><a id="price">€ 9.00</a>
                    <strong>🍻Bibite & Birre🍻</strong>
                    <a id="article">Acqua Naturale/Frizzante</a><a id="price">€ 1.00</a>
                    <a id="article">Soft drinks</a><a id="price">€ 2.50</a>
                    <a id="article">Birra alla spina 20cl</a><a id="price">€ 3.50</a>
                    <a id="article">Birra alla spina 40cl</a><a id="price">€ 5.00</a>
                    <a id="article">Birre Speciali in bottiglia</a><a id="price">€ 5.00</a>
                    <a id="article">Monster/Red Bull</a><a id="price">€ 3.50</a>
                    <strong>🍸Cocktail & Bowling Nigh🍸</strong>
                    <a id="article">Spritz (Aperol/Campari)</a><a id="price">€ 6.50</a>
                    <a id="article">Gin Tonic/Lemon</a><a id="price">€ 6.50</a>
                    <a id="article">Vodka Tonic/Lemon</a><a id="price">€ 6.00</a>
                    <a id="article">Mojito</a><a id="price">€ 7.00</a>
                    <a id="article">Long Island Iced Tea</a><a id="price">€ 7.00</a>
                    <a id="article">Analcolico alla Frutta</a><a id="price">€ 5.00</a>
                    <strong>🍨Dolci e Caffè🍨</strong>
                    <a id="article">Waffle con Nutella e panna</a><a id="price">€ 5.50</a>
                    <a id="article">Coppa Gelato (3 gusti)</a><a id="price">€ 4.00</a>
                    <a id="article">Caffè Espresso</a><a id="price">€ 1.00</a>
                </div>

                </div>
                <img src="../resources/img_giochi/area_food.jpg">
                </div>
                
                <div class="food-description">
                    <p>
                        Appendi la stecca al chiodo e posa la carabina: è tempo di rifocillarsi nella nostra <strong>Area Food</strong>, 
                        il quartier generale del gusto! Tra un burger succulento e una pizza da campioni, lo strike più importante 
                        della serata è quello che farai seduto a tavola. <strong>Brinda con gli amici e goditi il lato più saporito del divertimento!</strong>
                    </p>
                </div>
            </div>

            <div class="faq-area">
                <h2 class="faq-section-title">Lascia una Recensione</h2>
                
                <?php if ($username): // Controllo se l'utente è loggato per mostrargli il modulo di inserimento ?>
                    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" onsubmit="return validaRecensione();">
                        <span class="label-voto">Seleziona il tuo voto:</span>
                        <div class="rating-stars">
                            <?php   // Genero 5 radio button per le stelle. Parto da 5 e scendo a 1 
                                    // per far funzionare correttamente l'effetto hover CSS (row-reverse)
                                for($i=5; $i>=1; $i--): ?>
                                <input type="radio" id="star<?= $i ?>" name="voto_stelle" value="<?= $i ?>" /><label for="star<?= $i ?>">★</label>
                            <?php endfor; ?>
                        </div>
                        <textarea 
                            id="testo_commento"
                            name="testo_commento" 
                            class="review-textarea" 
                            placeholder="Cosa ne pensi?" 
                            oninput='this.style.height = ""; this.style.height = this.scrollHeight + "px"'
                        ></textarea>
                            <button type="submit" name="invia_commento" class="btn-submit-review">PUBBLICA RECENSIONE</button>
                    </form>
                <?php else: ?>
                    <p class="login-alert"><a href="../login/login.php">Accedi</a> per poter scrivere una recensione.</p>
                <?php endif; ?>

                <div class="reviews-container">
                    <div class="reviews-header">
                        <h3 id="sezione-recensioni">Recensioni degli Utenti</h3>

                        <div class="filter-wrapper">
                            <span class="filter-label">Filtra per:</span>
                            <a href="?#sezione-recensioni" class="filter-link <?= !$filtro_stelle ? 'filter-all-active' : 'filter-all-inactive' ?>">Tutte</a>
                            <?php   // Apro un ciclo for decrescente per generare i link di filtro da 5 a 1 stella 
                                for($i=5; $i>=1; $i--): ?>
                                <a href="?filtro_stelle=<?= $i ?>#sezione-recensioni" class="filter-link <?= ($filtro_stelle == $i) ? 'filter-active' : 'filter-inactive' ?>"><?= $i ?> ★</a>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="comments-scroll-box">
                        <!-- Controllo se la query al database ha prodotto risultati e se l'oggetto del risultato esiste -->
                        <?php if ($risultato_faq && pg_num_rows($risultato_faq) > 0): ?>
                            <?php while ($f = pg_fetch_assoc($risultato_faq)): //ogni riga del database diventa un array associativo ($f) ?>
                                <div class="comment-card">
                                    <div>
                                        <strong class="comment-author">@<?= htmlspecialchars($f['username']) ?></strong>
                                        <span class="comment-date">il <?= date('d/m/Y', strtotime($f['data_recensione'])) ?></span>
                                        <?php // Controllo se l'utente ha lasciato un voto; se il campo 'stelle' non è vuoto, 
                                              // procedo alla generazione grafica delle icone. */
                                            if (!empty($f['stelle'])): ?>
                                            <span class="comment-stars"><?= str_repeat('★', $f['stelle']) ?>
                                                <span class="comment-stars-empty"><?= str_repeat('★', 5 - $f['stelle']) ?>
                                            <!-- Calcolo la differenza per arrivare a 5 stelle totali, le piene saranno il voto effettivo -->
                                            </span></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="comment-text">"<?= htmlspecialchars($f['recensione']) ?>"</div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>Nessuna recensione trovata.</p>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        </main>

        <aside class="sidebar-right">
            <p class="titolo-sidebar">ACCOUNT</p>
            <div>
                <?php if (isset($_SESSION['utente'])): ?>
                    <p> <p id="title-blue">Username</p> <?= htmlspecialchars($username) ?></p>
                    <p> <p id="title-blue">Email</p> <?= htmlspecialchars($email) ?></p>
                    
                    <p class="titolo-sidebar" style="margin-top: 40px;">PRENOTAZIONI ATTIVE</p>
                    <div id="carrello-box">
                        <!-- Controllo se la query alla tabella prenotazioni (eseguita prima nel PHP) ha prodotto risultati -->
                        <?php if ($res_sidebar && pg_num_rows($res_sidebar) > 0): ?>
                            <p style="color:#888;">Solo le più recenti:</p>
                            <ul>
                                <!-- Avvio un ciclo per scorrere i record delle prenotazioni trovate -->
                                <?php while ($item = pg_fetch_assoc($res_sidebar)): 
                                    $data_f = date('d M, H:i', strtotime($item['data_ora'])); //Formatto la data
                                ?>
                                    <li id="dettaglio-pren-gioco">
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
                    <p style="font-style: italic; line-height: 1.6; color: #ccc;">
                        Non sei ancora dei nostri?<br>
                        Registrati subito per accedere<br>
                        a tutti i servizi del club<br>
                        e gestire le tue prenotazioni!
                    </p>
                <?php endif; ?>
            </div>
        </aside>

        <footer>
            © 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21
        </footer>

        <script>
            // Definisco la funzione che controllerà la validità del modulo prima dell'invio
            function validaRecensione() {
                // Controllo Stelle: cerco tra i radio button quello che è stato selezionato (checked)
                // Utilizzo document.querySelector per individuare l'input con nome "voto_stelle" spuntato
                const stelle = document.querySelector('input[name="voto_stelle"]:checked');
                if (!stelle) {
                    alert("Attenzione: Devi selezionare un voto (da 1 a 5 stelle)!");
                    return false; // Blocca l'invio
                }

                // Controllo Testo: recupero il contenuto della textarea tramite il suo ID unico
                // Uso .trim() per eliminare eventuali spazi vuoti iniziali o finali inseriti per errore
                const testo = document.getElementById('testo_commento').value.trim();
                if (testo === "") {
                    alert("Attenzione: Inserisci il testo della tua recensione!");
                    document.getElementById('testo_commento').focus(); // Riporta il cursore nella casella
                    return false; // Blocca l'invio
                }

                // Restituisco true per permettere al browser di procedere con l'invio dei dati al database
                return true;
            }
        </script>

    </body>
</html>