<?php
include "db.php";
session_start();

// Controllo se l'utente è loggato
if (!isset($_SESSION['utente'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['utente'];
$nome_completo = $_SESSION['nome'];
$email = $_SESSION['email'];

// 1. Gestione eliminazione prenotazione
if (isset($_POST['elimina_prenotazione'])) {
    $id_pren = $_POST['id_prenotazione'];
    $sql_delete = "DELETE FROM prenotazioni WHERE id_prenotazione = $1 AND username_utente = $2";
    pg_query_params($db, $sql_delete, array($id_pren, $username));
}

// 2. Recupero prenotazioni attive dell'utente
// Ordiniamo per data_ora così le più vicine appaiono per prime
$sql_select = "SELECT * FROM prenotazioni 
               WHERE username_utente = $1 
               AND data_ora >= CURRENT_TIMESTAMP 
               ORDER BY data_ora ASC";
$risultato_pren = pg_query_params($db, $sql_select, array($username));

// Pulizia automatica prenotazioni scadute
$sql_clean = "DELETE FROM prenotazioni WHERE data_ora < CURRENT_TIMESTAMP";
pg_query($db, $sql_clean);

// 3. Recupero giochi per la sidebar sinistra
$risultatoGiochi = pg_query($db, "SELECT nome_gioco FROM giochi ORDER BY nome_gioco;");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Le Mie Prenotazioni - The Bowler Club</title>
    <link rel="stylesheet" href="mainpage.css">
    <link rel="stylesheet" href="prenotazioni.css">
</head>

<body>

<header>
    <div class="site" onclick="window.location.href='index.php'">
        <img src="resources/logo.png" class="logo">
        <h1>The Bowler Club</h1>
    </div>
    <div class="user">
        <h2>Ciao <?= htmlspecialchars($_SESSION['nome']) ?></h2>
    </div>
</header>

<aside class="sidebar-left">
    <p class="titolo-sidebar">SERVIZI OFFERTI</p>
    <ul>
        <li><a href="index.php">Torna alla Home</a></li>
        <?php while ($r = pg_fetch_assoc($risultatoGiochi)): ?>
            <li><a href="dettaglio_gioco.php?gioco=<?= urlencode($r['nome_gioco']) ?>"><?= htmlspecialchars($r['nome_gioco']) ?></a></li>
        <?php endwhile; ?>
        <li><a href="area_faq.php">Area Food e Recensioni</a></li>
    </ul>
</aside>

<main>
    <h1 class="titolo" >Gestione Prenotazioni</h1>
    
    <?php
    if (pg_num_rows($risultato_pren) > 0):
        $prenotazioni_per_data = [];
        while ($pren = pg_fetch_assoc($risultato_pren)) {
            $data = date('d/m/Y', strtotime($pren['data_ora']));
            $prenotazioni_per_data[$data][] = $pren;
        }

        foreach ($prenotazioni_per_data as $data => $lista_pren): ?>
            
            <h2>
                <?= $data ?> 
                <span style="justify-self: right;">
                    (<?= count($lista_pren) ?> <?= count($lista_pren) == 1 ? 'prenotazione' : 'prenotazioni' ?>)
                </span>
            </h2>
            
            <hr>

            <div class="carousel-container">
                <div class="carousel-wrapper">
                    <?php foreach ($lista_pren as $pren): ?>
                        <div class="card-gioco-carousel">
                            <div class="card-content" >
                                <h3 ><?= htmlspecialchars($pren['nome_gioco']) ?></h3>
                                <p>
                                    Ore <?= date('H:i', strtotime($pren['data_ora'])) ?>
                                </p>
                                
                                <div id="info_gioco">
                                    <?php if ($pren['numero_tavolo']): ?>
                                        <p>🎱 Tavolo: <?= htmlspecialchars($pren['numero_tavolo']) ?></p>
                                    
                                    <?php elseif ($pren['numero_pista']): ?>
                                        <p>🎳 Pista: <?= htmlspecialchars($pren['numero_pista']) ?></p>

                                    <?php elseif ($pren['numero_persone']): ?>
                                        <p>👥 Persone: <?= htmlspecialchars($pren['numero_persone']) ?></p>
                                    
                                    <?php else: ?>
                                        <p>🃏 Carte: Iscritto con successo</p>
                                    <?php endif; ?>
                                </div>

                                <form method="POST" onsubmit="return confirm('Annullare questa prenotazione?');">
                                    <input type="hidden" name="id_prenotazione" value="<?= $pren['id_prenotazione'] ?>">
                                    <button type="submit" name="elimina_prenotazione">
                                        Annulla
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php endforeach; ?>

    <?php else: ?>
        <div>
            <p>Non hai ancora effettuato prenotazioni attive.</p>
            <button onclick="window.location.href='index.php'">Prenota ora</button>
        </div>
    <?php endif; ?>
</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">ACCOUNT</p>
    <div >
        <p> <p id="title-blue"> Username </p> <?= htmlspecialchars($username) ?></p>
        <p> <p id="title-blue"> Email </p> <?= htmlspecialchars($email) ?></p>
        
        <button name="logout" onclick="window.location.href='logout.php'">Logout</button>

        <form action="elimina_account.php" method="POST" onsubmit="return confirm('ATTENZIONE: Sei sicuro di voler eliminare definitivamente il tuo account? Questa operazione cancellerà anche tutte le tue prenotazioni e non è reversibile.');" style="margin-top: 10px;">
            <button type="submit" name="conferma_eliminazione">
                Elimina Account
            </button>
        </form>
    </div>
</aside>

<footer>
    © 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21
</footer>

</body>
</html>