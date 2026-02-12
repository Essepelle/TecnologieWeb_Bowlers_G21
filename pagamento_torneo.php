<?php
include "db.php";
session_start();

if (!isset($_SESSION['pending_reservation'])) {
    header("Location: mainpage.php");
    exit();
}

$dati = $_SESSION['pending_reservation'];
// $dati[1] è il nome gioco, $dati[2] è data_ora
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Pagamento Torneo - The Bowler Club</title>
    <link rel="stylesheet" href="mainpage.css">
    <link rel="stylesheet" href="pagamento_torneo.css">
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


    <div class="payment-container">
        <h2>Conferma Partecipazione Torneo</h2>
        <div class="recap-box">
            <p>Gioco: <strong><?= htmlspecialchars($dati[1]) ?></strong></p>
            <p>Data e Ora: <strong><?= htmlspecialchars($dati[2]) ?></strong></p>
            <p>Quota Iscrizione: <strong>€ 5.00</strong></p>
        </div>

        <form action="processa_pagamento.php" method="POST" class="payment-form">
            <label>Titolare Carta</label>
            <input type="text" placeholder="Nome Cognome" required>
            
            <label>Numero Carta</label>
            <input type="text" placeholder="1234 5678 1234 5678" maxlength="16" required>
            
            <div class="form-row">
                <div>
                    <label>Scadenza</label>
                    <input type="text" placeholder="MM/AA" maxlength="5" required>
                </div>
                <div>
                    <label>CVV</label>
                    <input type="text" placeholder="123" maxlength="3" required>
                </div>
            </div>
            
            <button type="submit" class="btn-pay">PAGA ORA</button>
            <a href="dettaglio_gioco.php?gioco=Carte" class="btn-cancel">Annulla</a>
        </form>
    </div>
</body>
</html>