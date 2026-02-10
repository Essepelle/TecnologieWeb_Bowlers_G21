<?php
include "db.php"; 
session_start();

// Recupero username dalla sessione (necessario per la query sidebar)
$username = $_SESSION['utente'] ?? '';

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
        <li><a href="#presentazione">Home</a></li>
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


                    <!-- Si discrimina l'utente registrato da quello non registrato -->
                    <?php if (isset($_SESSION['utente'])): ?>
                    <!-- utente loggato -->
                    <button type="button"
                        onclick="window.location.href='dettaglio_gioco.php?gioco=<?= urlencode($row['nome_gioco']) ?>'">
                        Prenota
                    </button>
                    <?php else: ?>
                    <!-- utente NON loggato -->
                        <button type="button"
                            onclick="window.location.href='login.html?redirect=prenota&gioco=<?= urlencode($row['nome_gioco']) ?>'">
                            Prenota
                        </button>
                    <?php endif; ?>


                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">PRENOTAZIONI ATTIVE</p>
    <div id="carrello-box" style="font-size: 0.9em;">
        <?php if ($res_sidebar && pg_num_rows($res_sidebar) > 0): ?>
            <ul style="list-style: none; padding: 0;">
                <?php while ($item = pg_fetch_assoc($res_sidebar)): 
                    // Formattiamo la data per renderla più bella (es. 12 Feb, 21:00)
                    $data_f = date('d M, H:i', strtotime($item['data_ora']));
                ?>
                    <li style="margin-bottom: 15px; border-bottom: 1px solid #ff00ff40; padding-bottom: 5px;">
                        <strong style="color: #00b7ff; text-transform: uppercase;">
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
</aside>

<footer>
    <div id="footer-box">
        <p>© 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21</p>
    </div>
</footer>

<script>
document.querySelectorAll('.sidebar-left a').forEach(link => {
    link.addEventListener('click', function () {
        const targetId = this.getAttribute('href').substring(1);
        const targetDiv = document.getElementById(targetId);

        if (targetDiv) {
            // rimuove eventuali evidenziazioni precedenti
            document.querySelectorAll('.evidenzia, .fade').forEach(el => {
                el.classList.remove('evidenzia', 'fade');
            });

            // aggiunge evidenzia
            targetDiv.classList.add('evidenzia');

            // dopo 3 secondi, avvia fade
            setTimeout(() => {
                targetDiv.classList.add('fade');
            }, 1000);

            // rimuove completamente 
            setTimeout(() => {
                targetDiv.classList.remove('evidenzia', 'fade');
            }, 2000);
        }
    });
});
</script>



</body>
</html>