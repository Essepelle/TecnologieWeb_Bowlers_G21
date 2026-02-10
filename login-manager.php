<?php
include 'db.php';
session_start();
?>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Gestione Login - The Bowler Club</title>
    <link rel="stylesheet" type="text/css" href="login.css"/>
</head>
<body style="display: block; padding: 50px;">
    <div class="form" style="margin: 0 auto; width: 50%; text-align: center;">
    <?php
        // Verifichiamo che i dati siano stati inviati dal form
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Recuperiamo l'intera riga dell'utente dal DB
            $userRow = get_user($username, $db);

            if (!$userRow) {
                echo "<h2>Errore</h2>";
                echo "<p>L'username <strong>" . htmlspecialchars($username) . "</strong> non esiste.</p>";
                echo "<a href='login.html' class='button'>Riprova</a>";
            } else {
                // Verifichiamo la password usando la colonna 'pass' del tuo DB
                if (password_verify($password, $userRow['pass'])) {
                    // Login riuscito: inizializziamo la sessione
                    $_SESSION['utente'] = $userRow['username'];
                    $_SESSION['nome']   = $userRow['nome_completo'];
                    $_SESSION['email']  = $userRow['email'];

                    // Reindirizzamento alla home del sito
                    header("Location: mainpage.php");
                    exit;
                } else {
                    echo "<h2>Accesso Negato</h2>";
                    echo "<p>Username o password errati.</p>";
                    echo "<a href='login.html' class='button'>Riprova</a>";
                }
            }
        } else {
            echo "<h2>Errore</h2>";
            echo "<p>Inserire username e password per accedere.</p>";
            echo "<a href='login.html' class='button'>Torna al Login</a>";
        }
    ?>
    </div>
</body>
</html>

<?php
/**
 * Recupera i dati dell'utente basandosi sullo username
 * Adeguato alla tua tabella: username, nome_completo, email, pass
 */
function get_user($user, $db) {
    // Usiamo i nomi esatti delle tue colonne
    $sql = "SELECT username, nome_completo, email, pass 
            FROM utenti 
            WHERE username = $1;";
    
    // Usiamo pg_query_params per semplicità e sicurezza
    $ret = pg_query_params($db, $sql, array($user));

    if (!$ret) {
        // Opzionale: log dell'errore per debug
        // echo "ERRORE QUERY: " . pg_last_error($db);
        return false;
    }

    if ($row = pg_fetch_assoc($ret)) {
        return $row; // Restituisce l'array con username, nome_completo, email e pass
    } else {
        return false;
    }
}
?>