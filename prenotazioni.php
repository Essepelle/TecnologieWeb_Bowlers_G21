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
<body>

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
    </ul>
</aside>

<main>
    <div style="padding: 20px;">
        <h1 style="color: #ff00ff; text-align: center;">Gestione Prenotazioni</h1>
        <p style="text-align: center;">Qui puoi visualizzare e annullare le tue prenotazioni.</p>

        <?php if (pg_num_rows($risultato_pren) > 0): ?>
            <div class="grid-giochi">
                <?php while ($pren = pg_fetch_assoc($risultato_pren)): ?>
                    <div class="card-gioco-odd" style="grid-template-columns: 100%; margin-bottom: 10px; border: 1px solid #ff00ff80;">
                        <div style="padding: 15px;">
                            <h2 style="color: #00b7ff;"><?= htmlspecialchars($pren['nome_gioco']) ?></h2>
                            <p><strong>Data e Ora:</strong> <?= date('d/m/Y H:i', strtotime($pren['data_ora'])) ?></p>
                            
                            <?php if ($pren['numero_tavolo']): ?>
                                <p><strong>Tavolo:</strong> <?= htmlspecialchars($pren['numero_tavolo']) ?></p>
                            <?php endif; ?>
                            
                            <?php if ($pren['numero_pista']): ?>
                                <p><strong>Pista:</strong> <?= htmlspecialchars($pren['numero_pista']) ?></p>
                            <?php endif; ?>

                            <?php if ($pren['numero_persone']): ?>
                                <p><strong>Persone:</strong> <?= htmlspecialchars($pren['numero_persone']) ?></p>
                            <?php endif; ?>

                            <?php if ($pren['partecipazione_torneo'] == 't'): ?>
                                <p style="color: #00ff00;">✓ Iscritto al Torneo</p>
                            <?php endif; ?>

                            <form method="POST" onsubmit="return confirm('Sei sicuro di voler annullare questa prenotazione?');">
                                <input type="hidden" name="id_prenotazione" value="<?= $pren['id_prenotazione'] ?>">
                                <button type="submit" name="elimina_prenotazione" style="background-color: #ff4d4d; margin-top: 10px;">
                                    Annulla Prenotazione
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; margin-top: 50px;">
                <p>Non hai ancora effettuato prenotazioni.</p>
                <button onclick="window.location.href='mainpage.php'">Prenota ora</button>
            </div>
        <?php endif; ?>
    </div>
</main>

<aside class="sidebar-right">
    <p class="titolo-sidebar">INFO ACCOUNT</p>
    <div id="carrello-box">
        <p>Username: <strong><?= htmlspecialchars($username) ?></strong></p>
        <p>Nome Completo: <strong><?= htmlspecialchars($nome_completo) ?></strong></p>
        <p>Email: <strong><?= htmlspecialchars($email) ?></strong></p>
        <button onclick="window.location.href='logout.php'" style="width: 100%;">Logout</button>

        <form action="elimina_account.php" method="POST" onsubmit="return confirm('ATTENZIONE: Sei sicuro di voler eliminare definitivamente il tuo account? Questa operazione cancellerà anche tutte le tue prenotazioni e non è reversibile.');">
            <button type="submit" name="conferma_eliminazione" style="width: 100%; background-color:rgb(56, 56, 56); color: white; font-weight: bold; border: none; padding: 10px; cursor: pointer;">
                Elimina Account
            </button>
        </form>
    </div>
</aside>

<footer>
    <div id="footer-box">
        <p>© 2026 - The Bowler Club</p>
    </div>
</footer>

</body>
</html>