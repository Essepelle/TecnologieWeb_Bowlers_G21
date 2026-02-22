<?php
include "../db.php";
session_start();

// Se non ci sono dati di prenotazione in sospeso, torna alla home
if (!isset($_SESSION['pending_reservation'])) {
    header("Location: ../index.php");
    exit();
}

$dati = $_SESSION['pending_reservation'];
// $dati[1] è il nome del gioco, $dati[2] è la data e l'ora

// recupero errori lato server e li ripulisco dalla sessione
$error_payment = $_SESSION['error_payment'] ?? '';
unset($_SESSION['error_payment']);

// recupero vecchi dati per lo sticky form
$old_data = $_SESSION['old_payment'] ?? [];
unset($_SESSION['old_payment']);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Pagamento Torneo - The Bowler Club</title>
    <link rel="stylesheet" href="../mainpage.css">
    <link rel="stylesheet" href="pagamento_torneo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="icon" href="../resources/logo.png"/>
</head>
<body>
    <header>
        <div class="site" onclick="window.location.href='../index.php'">
            <img src="../resources/logo.png" class="logo" alt="Logo">
            <h1>The Bowler Club</h1>
        </div>
        <div class="user">
            <?php if (isset($_SESSION['utente'])): ?>
                <div class="dropdown-container">
                    <h2 style="cursor: pointer;">
                        <?php 
                            // Dividiamo il nome completo in un array usando lo spazio come separatore
                            $parti_nome = explode(' ', trim($_SESSION['nome'])); 
                            // Prendiamo solo la prima parola
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
                <h2 onclick="window.location.href='../login/login.php'" style="cursor:pointer;">Effettua il Login</h2>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <div class="payment-container">
            <h1>Conferma Partecipazione Torneo</h1>
            <?php if (!empty($error_payment)): ?>
            <div class="error-box">
                <i class="fa-solid fa-triangle-exclamation"></i> 
                <?php echo htmlspecialchars($error_payment); ?>
                <i class="fa-solid fa-triangle-exclamation"></i> 
            </div>
            <?php endif; ?>
            
            <div class="recap-box">
                <p>Gioco: <strong><?= htmlspecialchars($dati[1]) ?></strong></p>
                <p>Data e Ora: <strong><?= htmlspecialchars($dati[2]) ?></strong></p>
                <p>Quota Iscrizione: <strong>€ 5.00</strong></p>
            </div>

            <form action="processo_pagamento.php" method="POST" onsubmit="return validaPagamento();">
            <div class="payment-form">    
                <label>Titolare Carta</label>
                    <input type="text" id="titolare" name="titolare" placeholder="Nome Cognome"
                    value="<?= htmlspecialchars($old_data['titolare'] ?? '') ?>">
    
                <label>Numero Carta</label>
                    <input type="text" id="numero_carta" name="numero_carta" placeholder="1234567812345678" maxlength="16"
                    value="<?= htmlspecialchars($old_data['numero_carta'] ?? '') ?>">
    
            <div class="form-row">
                <label>Scadenza</label>
                <label>CVV</label>
                    <input type="text" id="scadenza" name="scadenza" placeholder="MM/AA" maxlength="5">
               
                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3">
            </div>    
                <input type="submit" name="esegui_pagamento" class="btn-pay" value="PAGA ORA">
                <a href="../dettaglio_gioco/dettaglio_gioco.php?gioco=Torneo di Carte" class="btn-cancel">Annulla</a>
            </div>
            </form>
        </div>
    </main>

    <footer>
        © 2026 - The Bowler Club - Pascariello Vincenzo, Pellecchia Simone, Turi Martina - Bowlers G21
    </footer>

    <script>
        // Funzione per validare tutto il form di pagamento
        function validaPagamento() {
            const titolare = document.getElementById('titolare').value.trim();
            const numeroCarta = document.getElementById('numero_carta').value.trim();
            const scadenza = document.getElementById('scadenza').value.trim();
            const cvv = document.getElementById('cvv').value.trim();

            // 1. Controllo Titolare (solo lettere e spazi, min 5 caratteri)
            const regexTitolare = /^[A-Za-z\s]{5,}$/;
            if (!titolare) {
                alert("Attenzione: Inserisci il nome del titolare della carta.");
                return false;
            } else if (!regexTitolare.test(titolare)) {
                alert("Attenzione: Il nome del titolare deve contenere solo lettere e spazi (minimo 5 caratteri).");
                return false;
            }

            // 2. Controllo Numero Carta (esattamente 16 cifre)
            const regexCarta = /^\d{16}$/;
            if (!numeroCarta) {
                alert("Attenzione: Inserisci il numero della carta.");
                return false;
            } else if (!regexCarta.test(numeroCarta)) {
                alert("Attenzione: Il numero della carta deve contenere esattamente 16 cifre, senza spazi o trattini.");
                return false;
            }

            // 3. Controllo Formato Scadenza (MM/AA)
            const regexScadenza = /^(0[1-9]|1[0-2])\/\d{2}$/;
            if (!scadenza) {
                alert("Attenzione: Inserisci la data di scadenza.");
                return false;
            } else if (!regexScadenza.test(scadenza)) {
                alert("Attenzione: Il formato della scadenza deve essere MM/AA (es. 05/26).");
                return false;
            }
            // 4. Controllo CVV (esattamente 3 cifre)
            const regexCVV = /^\d{3}$/;
            if (!cvv) {
                alert("Attenzione: Inserisci il CVV (codice di 3 cifre).");
                return false;
            } else if (!regexCVV.test(cvv)) {
                alert("Attenzione: Il CVV deve contenere esattamente 3 cifre.");
                return false;
            }

            // Se tutti i controlli vengono superati
            return true;
        }

        // Questo mantiene la comodità per l'utente: aggiunge lo slash '/' in automatico!
        document.getElementById('scadenza').addEventListener('input', function(e) {
            if (e.target.value.length === 2 && e.inputType !== 'deleteContentBackward') {
                e.target.value += '/';
            }
        });
    </script>

</body>
</html>