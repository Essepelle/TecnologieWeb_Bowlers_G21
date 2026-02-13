<?php
include "db.php";
session_start();

// Se non ci sono dati di prenotazione in sospeso, torna alla home
if (!isset($_SESSION['pending_reservation'])) {
    header("Location: index.php");
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
        <div class="site" onclick="window.location.href='index.php'">
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
            <h1>Conferma Partecipazione Torneo</h1>
            
            <div class="recap-box">
                <p>Gioco: <strong><?= htmlspecialchars($dati[1]) ?></strong></p>
                <p>Data e Ora: <strong><?= htmlspecialchars($dati[2]) ?></strong></p>
                <p>Quota Iscrizione: <strong>€ 5.00</strong></p>
            </div>

            <form action="processo_pagamento.php" method="POST">
            <div class="payment-form">    
                <label>Titolare Carta</label>
                <input type="text" name="titolare" placeholder="Nome Cognome" required 
                    pattern="[A-Za-z\s]{5,}" title="Inserisci il nome completo del titolare">
                
                <label>Numero Carta</label>
                <input type="text" name="numero_carta" placeholder="1234567812345678" 
                    maxlength="16" minlength="16" required 
                    pattern="\d{16}" title="Inserisci le 16 cifre della carta senza spazi">
                
                <div class="form-row">
                    <label>Scadenza</label>
                    <label>CVV</label>
                    <input type="text" id="scadenza" name="scadenza" placeholder="MM/AA" 
                        maxlength="5" required 
                        pattern="(0[1-9]|1[0-2])\/[0-9]{2}" title="Formato MM/AA">
                    <input type="text" id="cvv" name="cvv" placeholder="123" 
                        maxlength="3" minlength="3" required 
                        pattern="\d{3}" title="Le 3 cifre sul retro della carta">
                </div>
                
                <input type="submit" name="esegui_pagamento" class="btn-pay" value="PAGA ORA">
                <a href="dettaglio_gioco.php?gioco=Carte" class="btn-cancel">Annulla</a>
            </div>
            </form>
        </div>
    </main>

    <footer>
        © 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21
    </footer>

    <script>
        document.querySelector('.payment-form').addEventListener('submit', function(e) {
            const scadenza = document.getElementsByName('scadenza')[0].value;
            const [mese, anno] = scadenza.split('/').map(n => parseInt(n, 10));
            
            const oggi = new Date();
            const meseCorrente = oggi.getMonth() + 1;
            const annoCorrente = parseInt(oggi.getFullYear().toString().slice(-2), 10);

            // Controllo validità temporale
            if (anno < annoCorrente || (anno === annoCorrente && mese < meseCorrente)) {
                alert("La carta è scaduta. Inserisci una data valida.");
                e.preventDefault(); // Blocca l'invio del form
            }
        });
    </script>

</body>
</html>