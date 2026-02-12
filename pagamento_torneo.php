<?php
include "db.php";
session_start();

// Se non ci sono dati di prenotazione in sospeso, torna alla home
if (!isset($_SESSION['pending_reservation'])) {
    header("Location: mainpage.php");
    exit();
}

$dati = $_SESSION['pending_reservation'];
// $dati[1] è il nome del gioco, $dati[2] è la data e l'ora
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Pagamento Torneo - The Bowler Club</title>
    <link rel="stylesheet" href="mainpage.css">
    <link rel="stylesheet" href="pagamento_torneo.css">
    <link rel="icon" type="icon" href="resources/logo.png"/>
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

    <main>
        <div class="payment-container">
            <h2>Conferma Partecipazione Torneo</h2>
            
            <div class="recap-box">
                <p>Gioco: <strong><?= htmlspecialchars($dati[1]) ?></strong></p>
                <p>Data e Ora: <strong><?= htmlspecialchars($dati[2]) ?></strong></p>
                <p>Quota Iscrizione: <strong>€ 5.00</strong></p>
            </div>

            <form action="processo_pagamento.php" method="POST" class="payment-form">
                <label>Titolare Carta</label>
                <input type="text" name="titolare" placeholder="Nome Cognome" required 
                    pattern="[A-Za-z\s]{5,}" title="Inserisci il nome completo del titolare">
                
                <label>Numero Carta</label>
                <input type="text" name="numero_carta" placeholder="1234567812345678" 
                    maxlength="16" minlength="16" required 
                    pattern="\d{16}" title="Inserisci le 16 cifre della carta senza spazi">
                
                <div class="form-row">
                    <div>
                        <label>Scadenza</label>
                        <input type="text" name="scadenza" placeholder="MM/AA" 
                            maxlength="5" required 
                            pattern="(0[1-9]|1[0-2])\/[0-9]{2}" title="Formato MM/AA">
                    </div>
                    <div>
                        <label>CVV</label>
                        <input type="text" name="cvv" placeholder="123" 
                            maxlength="3" minlength="3" required 
                            pattern="\d{3}" title="Le 3 cifre sul retro della carta">
                    </div>
                </div>
                
                <button type="submit" name="esegui_pagamento" class="btn-pay">PAGA ORA</button>
                <a href="dettaglio_gioco.php?gioco=Carte" class="btn-cancel">Annulla e Torna Indietro</a>
            </form>
        </div>
    </main>

    <footer>
        <div id="footer-box">
            <p>© 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21</p>
        </div>
    </footer>
</body>
</html>