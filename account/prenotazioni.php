<?php
include "../db.php";
session_start();

// Includo il database e controllo che l'utente sia loggato; se non lo è, lo rimando al login
if (!isset($_SESSION['utente'])) {
    header("Location: ../login/login.php");
    exit();
}

$username = $_SESSION['utente'];
$nome_completo = $_SESSION['nome'];
$email = $_SESSION['email'];

// Se ricevo una richiesta POST, elimino la prenotazione specifica dal database
if (isset($_POST['elimina_prenotazione'])) {
    $id_pren = $_POST['id_prenotazione'];
    $sql_delete = "DELETE FROM prenotazioni WHERE id_prenotazione = $1 AND username_utente = $2";
    pg_query_params($db, $sql_delete, array($id_pren, $username));
}

// Recupero dal DB le prenotazioni attive dell'utente in ordine cronologico
$sql_select = "SELECT * FROM prenotazioni 
               WHERE username_utente = $1 
               AND data_ora >= CURRENT_TIMESTAMP 
               ORDER BY data_ora ASC";
$risultato_pren = pg_query_params($db, $sql_select, array($username));

// Eseguo una pulizia automatica cancellando tutte le prenotazioni passate, quindi scadute
$sql_clean = "DELETE FROM prenotazioni WHERE data_ora < CURRENT_TIMESTAMP";
pg_query($db, $sql_clean);

// Carico la lista dei giochi disponibili per popolare la sidebar sinistra
$risultatoGiochi = pg_query($db, "SELECT nome_gioco FROM giochi ORDER BY nome_gioco;");
?>






<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Le Mie Prenotazioni - The Bowler Club</title>
        <link rel="stylesheet" href="../mainpage.css">
        <link rel="stylesheet" href="prenotazioni.css">
    </head>

    <body>
        <header>
            <div class="site" onclick="window.location.href='../index.php'">
                <img src="../resources/logo.png" class="logo">
                <h1>The Bowler Club</h1>
            </div>
            <div class="user">
                <h2 style="cursor: pointer;">
                    <?php 
                        // Dividio il nome completo in un array usando lo spazio come separatore, 
                        // in modo da prendere solo il nome
                        $parti_nome = explode(' ', trim($_SESSION['nome'])); 
                        $primo_nome = $parti_nome[0]; 
                    ?>
                    Ciao <?= htmlspecialchars($primo_nome) ?>
                </h2>
            </div>
        </header>

        <aside class="sidebar-left">
            <p class="titolo-sidebar">SERVIZI OFFERTI</p>
            <ul>
                <li><a href="../index.php">Torna alla Home</a></li>
                <!-- Apro un ciclo while che continua finché ci sono righe nella variabile $risultatoGiochi e -
                    verrà ripetuto tante volte quanti sono i giochi presenti nella tabella del database -->
                <?php pg_result_seek($risultatoGiochi, 0);  //riportare il "puntatore" del risultato della query all'inizio (riga 0).
                    while ($r = pg_fetch_assoc($risultatoGiochi)): ?>
                    <li><a href="../dettaglio_gioco/dettaglio_gioco.php?gioco=<?= urlencode($r['nome_gioco']) ?>"><?= htmlspecialchars($r['nome_gioco']) ?></a></li>
                <?php endwhile; ?>
                <li><a href="../area_faq/area_faq.php">Area Food e Recensioni</a></li>
            </ul>
        </aside>

        <main>
            <h1 id="titolo" >Gestione Prenotazioni</h1>
            
            <?php
                // Controllo se il database mi ha restituito almeno una prenotazione attiva
                if (pg_num_rows($risultato_pren) > 0):
                    $prenotazioni_per_data = [];    //array vuoto per raggruppare le prenotazioni
                    
                // Estraggo una riga alla volta dal risultato della query finché ce ne sono
                while ($pren = pg_fetch_assoc($risultato_pren)) {
                    $data = date('d/m/Y', strtotime($pren['data_ora']));    //formatto il risultato
                    // Organizzo l'array in modo che ogni data diventa una "chiave" che contiene una lista di prenotazioni
                    $prenotazioni_per_data[$data][] = $pren;
                }
                // Inizio a ciclare le date, per ogni giorno creo una sezione dedicata
                foreach ($prenotazioni_per_data as $data => $lista_pren): 
            ?>
                    
            <div class="data-num">
                <h2 id="data"><?= $data ?></h2> 
                <!-- Uso l'operatore ternario per gestire correttamente singolare/plurale -->
                <h2 id="num"><?= count($lista_pren) ?> <?= count($lista_pren) == 1 ? 'prenotazione' : 'prenotazioni' ?></h2>
            </div>
                    
            <hr>

            <div class="carousel-container">
                <div class="carousel-wrapper">
                <!-- Inizio un ciclo per ogni prenotazione presente nella data selezionata-->
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
                <div class="empty-state">
                    <p>Non hai ancora effettuato prenotazioni.</p>
                    <button onclick="window.location.href='../index.php'">Prenota ora</button>
                </div>
            <?php endif; ?>
        </main>

        <aside class="sidebar-right">
            <p class="titolo-sidebar">ACCOUNT</p>
            <div>
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