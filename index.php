<?php
include "db.php"; 
session_start();

// Recupero username dalla sessione (necessario per la query sidebar)
$username = $_SESSION['utente'] ?? "";
$email = $_SESSION['email'] ?? "";

/* 1) RECUPERA TUTTI I GIOCHI DAL DB */
$sqlGiochi = "SELECT nome_gioco, immagine FROM giochi ORDER BY nome_gioco;";
$risultatoGiochi = pg_query($db, $sqlGiochi);

if (!$risultatoGiochi) {
    die("Errore query giochi: " . pg_last_error($db));
}

/* 2) RECUPERO LE PRENOTAZIONI PER LA SIDEBAR DI DESTRA (Logica allineata a dettaglio_gioco.php) */
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
                    Ciao <?= htmlspecialchars($_SESSION['nome']) ?>
                </h2>
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
    <p class="titolo-sidebar">SERVIZI OFFERTI</p>
    <ul>
        <li><a href="#presentazione">Home</a></li>
        <?php 
        // Generazione dinamica dei link della sidebar basata sui giochi nel DB
        pg_result_seek($risultatoGiochi, 0);
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
        <button class="carousel-arrow left" onclick="scrollCarousel(-1)"><</button>
        <div class="carousel-wrapper" id="gameCarousel">
            <?php
            pg_result_seek($risultatoGiochi, 0);
            while ($row = pg_fetch_assoc($risultatoGiochi)):
                $anchorId = str_replace(' ', '-', $row['nome_gioco']);
                
                // Puntamento alla cartella descrizioni/
                $nomeFilePres = "descrizioni/pres_" . strtolower(str_replace(' ', '_', $row['nome_gioco'])) . ".txt";
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
                        onclick="window.location.href='dettaglio_gioco.php?gioco=<?= urlencode($row['nome_gioco']) ?>'">
                        Prenota
                    </button>
                    <?php else: ?>
                    <!-- utente non loggato -->
                    <button type="button"
                            onclick="window.location.href='login.php?redirect=prenota&gioco=<?= urlencode($row['nome_gioco']) ?>'">
                            Prenota
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>

            <div id="area-food" class="card-gioco-carousel">
                <img src="img/area_food.jpg" alt="Area Food">
                <div class="card-content">
                    <h1>Area Food & Recensioni</h1>
                    <p id="info_gioco">Vieni a scoprire la nostra selezione di snack, pizze e cocktail! <br>
                    Il posto perfetto per ricaricarsi tra una partita e l'altra.</p>

                    <button type="button" 
                        onclick="window.location.href='area_faq.php'">
                        Vai alle Recensioni
                    </button>
                </div>
            </div>
        </div>
        <button class="carousel-arrow right" onclick="scrollCarousel(1)">></button>
    </div>
 
    <div class="card-info">
        
        <div class="info-map">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2969.8!2d12.49!3d41.90!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z!5e0!3m2!1sit!2sit!4v1600000000000!5m2!1sit!2sit" 
                width="100%" 
                height="300" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        <div class="info-details"> 
            <h3>Vienici a trovare!</h3>
            <p>Siamo aperti tutti i giorni dalle <b>17:00</b> alle <b>02:00</b>.</p>
            
            <p class="contact-list" style="justify-content: flex-end;">
                <p>Via del Bowling, 123 - Roma <i class="fas fa-map-marker-alt"></i></li>
                <p>+39 012 345 6789 <i class="fas fa-phone"></i></li>
                <p>info@thebowlerclub.it <i class="fas fa-envelope"></i></li>
            </p>

            <div class="social-links">
                <h4>Seguici su:</h4>
                <a href="https://facebook.com" target="_blank" class="social-btn facebook">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
                <a href="https://instagram.com" target="_blank" class="social-btn instagram">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
            </div>
        </div>

    </div>

</main>

<!-- SCRIPT PER IL FUNZIONAMENTO DEL CAROSELLO -->
<script>
function scrollCarousel(direction) {
    const carousel = document.getElementById('gameCarousel');
    const scrollAmount = 320; // Larghezza card + gap
    carousel.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}
</script>


<aside class="sidebar-right">
    <p class="titolo-sidebar">ACCOUNT</p>
    <div style="font-size: 0.9em; color: #ccc;">
        <?php if (isset($_SESSION['utente'])): ?>
            <p style="overflow-wrap: break-word;"> <p id="title-blue">Username</p> <?= htmlspecialchars($username) ?></p>
            <p style="overflow-wrap: break-word;"> <p id="title-blue">Email</p> <?= htmlspecialchars($email) ?></p>
            
            <p class="titolo-sidebar" style="margin-top: 40px;">PRENOTAZIONI ATTIVE</p>
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

<script>
document.querySelectorAll('.sidebar-left a').forEach(link => {
    link.addEventListener('click', function (e) {
        // 1. Impediamo il comportamento standard del link (che farebbe saltare la pagina)
        e.preventDefault(); 

        const targetId = this.getAttribute('href').substring(1); // Prende l'ID (es. "Bowling")
        const targetDiv = document.getElementById(targetId);

        if (targetDiv) {
            // 2. Logica per scorrere il carosello fino alla card
            // 'inline: center' è la magia che centra l'elemento orizzontalmente
            targetDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center'
            });

            // 3. Rimuove l'evidenziazione da tutte le altre card (pulizia)
            document.querySelectorAll('.card-gioco-carousel').forEach(el => {
                el.classList.remove('evidenzia');
            });

            // 4. Aggiunge l'effetto visivo alla card trovata
            // Usiamo un piccolo ritardo per permettere allo scroll di iniziare
            setTimeout(() => {
                targetDiv.classList.add('evidenzia');
            }, 300);

            // 5. Rimuove l'effetto dopo 2.5 secondi
            setTimeout(() => {
                targetDiv.classList.remove('evidenzia');
            }, 1500);
        }
    });
});
</script>

</body>
</html>