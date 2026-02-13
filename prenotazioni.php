<?php
include "db.php";
session_start();

// Controllo se l'utente è loggato
if (!isset($_SESSION['utente'])) {
    header("Location: login.html");
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
</head>

<body style="grid-template-columns: 15% 1fr 15%;">

<header>
    <div class="site" onclick="window.location.href='mainpage.php'">
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
        <li><a href="mainpage.php">Torna alla Home</a></li>
        <?php while ($r = pg_fetch_assoc($risultatoGiochi)): ?>
            <li><a href="dettaglio_gioco.php?gioco=<?= urlencode($r['nome_gioco']) ?>"><?= htmlspecialchars($r['nome_gioco']) ?></a></li>
        <?php endwhile; ?>
        <li><a href="area_faq.php">Area Food e Recensioni</a></li>
    </ul>
</aside>

<main>
  
        
        <h1 class="titolo" style="padding: unset; margin-top: unset">Gestione Prenotazioni</h1>
        
        <p style="text-align: center; margin-bottom: 30px;">Qui puoi visualizzare e annullare le tue prenotazioni.</p>

        <?php if (pg_num_rows($risultato_pren) > 0): ?>
            <div class="grid-giochi">
                <?php while ($pren = pg_fetch_assoc($risultato_pren)): ?>
                    <div class="card-gioco-odd" style="
                        grid-template-columns: 100%; 
                        margin-bottom: 20px; /* Più spazio tra le card */
                        border: 2px solid #ff00ff80; /* Bordo fucsia leggermente più spesso */
                        border-radius: 15px; /* Angoli arrotondati */
                        background-color: rgba(30, 30, 30, 0.9); /* Sfondo scuro semitrasparente */
                        box-shadow: 0 5px 15px rgba(0,0,0,0.5); /* Ombra per dare profondità */
                        overflow: hidden; /* Assicura che il contenuto stia nei bordi arrotondati */
                    ">
                        <div style="padding: 20px;">
                            <h2 style="color: #00b7ff; margin-top: 0;"><?= htmlspecialchars($pren['nome_gioco']) ?></h2>
                            <p style="font-size: 1.1em;"><strong>Data e Ora:</strong> <?= date('d/m/Y H:i', strtotime($pren['data_ora'])) ?></p>
                            
                            <?php if ($pren['numero_tavolo']): ?>
                                <p><strong>Tavolo:</strong> <?= htmlspecialchars($pren['numero_tavolo']) ?></p>
                            <?php endif; ?>
                            
                            <?php if ($pren['numero_pista']): ?>
                                <p><strong>Pista:</strong> <?= htmlspecialchars($pren['numero_pista']) ?></p>
                            <?php endif; ?>

                            <?php if ($pren['numero_persone']): ?>
                                <p><strong>Persone:</strong> <?= htmlspecialchars($pren['numero_persone']) ?></p>
                            <?php endif; ?>

                            <form method="POST" onsubmit="return confirm('Sei sicuro di voler annullare questa prenotazione?');" style="margin-top: 20px;">
                                <input type="hidden" name="id_prenotazione" value="<?= $pren['id_prenotazione'] ?>">
                                <button type="submit" name="elimina_prenotazione" style="background-color: #ff4d4d; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%;">
                                    Annulla Prenotazione
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; margin-top: 50px; padding: 30px; background: rgba(0,0,0,0.5); border-radius: 15px;">
                <p style="font-size: 1.2em;">Non hai ancora effettuato prenotazioni.</p>
                <button onclick="window.location.href='mainpage.php'" style="margin-top: 20px;">Prenota ora</button>
            </div>
        <?php endif; ?>

</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">ACCOUNT</p>
    <div style="font-size: 0.9em; color: #ccc;">
        <p style="overflow-wrap: break-word;"> <p id="title-blue"> Username </p> <?= htmlspecialchars($username) ?></p>
        <p style="overflow-wrap: break-word;"> <p id="title-blue"> Email </p> <?= htmlspecialchars($email) ?></p>
        
        <button onclick="window.location.href='logout.php'" style="width: 100%; margin-top: 10px; font-weight: bold;">Logout</button>

        <form action="elimina_account.php" method="POST" onsubmit="return confirm('ATTENZIONE: Sei sicuro di voler eliminare definitivamente il tuo account? Questa operazione cancellerà anche tutte le tue prenotazioni e non è reversibile.');" style="margin-top: 10px;">
            <button type="submit" name="conferma_eliminazione" style="width: 100%; font-weight: bold; border: none; padding: 10px; cursor: pointer;">
                Elimina Account
            </button>
        </form>
    </div>
</aside>

<footer>
    <div id="footer-box">
    <p>© 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21</p>
    </div>
</footer>

</body>
</html>