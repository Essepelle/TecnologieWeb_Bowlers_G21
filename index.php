<?php
include "db.php"; 
session_start();

// Recupero dei dati dell'utente dalla sessione.
$username = $_SESSION['utente'] ?? "";
$email = $_SESSION['email'] ?? "";

// Definizione della query SQL per prelevare nome e percorso immagine di tutti i giochi in ordine alfabetico
$sqlGiochi = "SELECT nome_gioco, immagine FROM giochi ORDER BY nome_gioco;";
$risultatoGiochi = pg_query($db, $sqlGiochi);

if (!$risultatoGiochi) {
    die("Errore query giochi: " . pg_last_error($db));
}

/* Gestione dinamica della sidebar: se l'utente è loggato, il sistema interroga il database 
   per recuperare le sue ultime 5 prenotazioni attive. */
$res_sidebar = null;
if (!empty($username)) {
    $sql_sidebar = "SELECT nome_gioco, data_ora FROM prenotazioni 
                    WHERE username_utente = $1 
                    AND data_ora >= CURRENT_TIMESTAMP
                    ORDER BY data_ora ASC LIMIT 5";
    $res_sidebar = pg_query_params($db, $sql_sidebar, array($username));
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
            <div class="site" onclick="window.location.href='index.php'">
                <img src="resources/logo.png" class="logo" alt="Logo">
                <h1>The Bowler Club</h1>
            </div>
            <div class="user">
                <?php if (isset($_SESSION['utente'])): ?>
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
                            <a href="account/prenotazioni.php">Le mie Prenotazioni</a>
                            <a href="account/logout.php">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <h2 onclick="window.location.href='login/login.php'" style="cursor:pointer;">Effettua il Login</h2>
                <?php endif; ?>
            </div>
        </header>

        <aside class="sidebar-left">
            <p class="titolo-sidebar">SERVIZI OFFERTI</p>
            <ul>
                <li><a href="#presentazione">Home</a></li>
                <?php 
                    pg_result_seek($risultatoGiochi, 0);    //riportare il "puntatore" del risultato della query all'inizio (riga 0).
                    while ($rowSide = pg_fetch_assoc($risultatoGiochi)): 
                        $anchorId = str_replace(' ', '-', $rowSide['nome_gioco']);
                ?>
                    <li><a href="#<?= $anchorId ?>"><?= htmlspecialchars($rowSide['nome_gioco']) ?></a></li>
                <?php endwhile; ?>
                <li><a href="#area-food">Area Food e Recensioni</a></li>
            </ul>
        </aside>

        <main>
            <h1 id="presentazione" class="titolo" style="font-size: 3em;">Vieni a scoprire il tempio del divertimento da The Bowler Club!</h1>
            
            <div class="carousel-container">
                <button class="carousel-arrow left" onclick="scrollCarousel(-1)">
                        <!-- Questa classe permette di far comparire nella freccia il carattere "<" senza alterare il funzionamento del codice-->
                        <i class="fa-solid fa-chevron-left"></i></button>
                
                <div class="carousel-wrapper" id="gameCarousel">
                    <?php
                        pg_result_seek($risultatoGiochi, 0);
                        while ($row = pg_fetch_assoc($risultatoGiochi)):

                            /* Creo un ID univoco per l'ancoraggio HTML sostituendo gli spazi nel nome del gioco con dei trattini,
                            utile per i collegamenti rapidi all'interno della pagina. */
                            $anchorId = str_replace(' ', '-', $row['nome_gioco']);
                        
                        // Puntamento alla cartella descrizioni
                        $nomeFilePres = "resources/descrizioni/pres_" . strtolower(str_replace(' ', '_', $row['nome_gioco'])) . ".txt";
                        $presentazione = file_exists($nomeFilePres) ? file_get_contents($nomeFilePres) : "Scopri di più cliccando su prenota.";
                    ?>

                    <div id="<?= $anchorId ?>" class="card-gioco-carousel">
                        <img src="<?= htmlspecialchars($row['immagine']) ?>" alt="<?= htmlspecialchars($row['nome_gioco']) ?>">
                        <div class="card-content">
                            <h1><?= htmlspecialchars($row['nome_gioco']) ?></h1>
                            <p id="info_gioco"><?= nl2br(htmlspecialchars($presentazione)) ?></p>


                            <!-- Si discrimina l'utente registrato da quello non registrato -->
                            <?php if (isset($_SESSION['utente'])): ?>
                            <!-- utente loggato -->
                            <button type="button"
                                onclick="window.location.href='dettaglio_gioco/dettaglio_gioco.php?gioco=<?= urlencode($row['nome_gioco']) ?>'">
                                Prenota
                            </button>
                            <?php else: ?>
                            <!-- utente non loggato -->
                            <button type="button"
                                    onclick="window.location.href='login/login.php?redirect=prenota&gioco=<?= urlencode($row['nome_gioco']) ?>'">
                                    Prenota
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>

                    <div id="area-food" class="card-gioco-carousel">
                        <img src="resources/img_giochi/area_food.jpg" alt="Area Food">
                        <div class="card-content">
                            <h1>Area Food & Recensioni</h1>
                            <p id="info_gioco">Vieni a scoprire la nostra selezione di snack, pizze e cocktail! <br>
                            Il posto perfetto per ricaricarsi tra una partita e l'altra.</p>

                            <button type="button" 
                                onclick="window.location.href='area_faq/area_faq.php'">
                                Vai alle Recensioni
                            </button>
                        </div>
                    </div>
                </div>
                <button class="carousel-arrow right" onclick="scrollCarousel(1)"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        
            <div class="card-info">
                    
                <div class="info-map">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.2345!2d14.8080!3d40.6558!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x133bc3983637e163%3A0xc64441584284d7a8!2sVia%20Picentino%2C%2023%2C%2084131%20Salerno%20SA!5e0!3m2!1sit!2sit!4v1700000000000" 
                        width="100%" 
                        height="300" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    <p id="distanza-info">Calcolo della distanza in corso...</p>
                </div>

                <div class="info-details"> 
                    <h3>Vienici a trovare!</h3>
                    <p>Siamo aperti tutti i giorni dalle <b>17:00</b> alle <b>02:00</b>.</p>
                    
                    <p class="contact-list">
                        <a style="color: white" target="blank" href="https://www.google.com/maps/place/Bowling+The+Club/@40.6435226,14.8595867,15.38z/data=!4m6!3m5!1s0x133bdd44b2c34d83:0x830d2ca79eaf0653!8m2!3d40.6437912!4d14.8652385!16s%2Fg%2F11b6j06d6z?entry=ttu&g_ep=EgoyMDI2MDIxMS4wIKXMDSoASAFQAw%3D%3D">
                            <p> Via Picentino, 23, 84131 Salerno SA 
                            <i class="fas fa-map-marker-alt"></i></li></a>
                        <p> 089 849884 <i class="fas fa-phone"></i></li>
                        <p> thebowlerclub@gmail.com <i class="fas fa-envelope"></i></li>
                    </p>

                    <div class="social-links">
                        <h4>Seguici su:</h4>
                        <a href="https://www.facebook.com/BowlingTheClubPontecagnano/photos" target="_blank" class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://www.instagram.com/bowling_pontecagnano?igsh=MXBhbTlsb2hxaGJ1MA==" target="_blank" class="social-btn instagram">
                            <i class="fab fa-instagram"></i> Instagram
                        </a>
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
                        <?php if ($res_sidebar && pg_num_rows($res_sidebar) > 0): ?>
                            <p style="color:#888;">Solo le più recenti:</p>
                            <ul>
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

    <script src="index.js"></script>

    </body>
</html>