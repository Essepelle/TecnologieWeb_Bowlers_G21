<?php
include 'db.php';
session_start();
?>
<html>
<head>
    <title>Esito Registrazione</title>
    <link rel="stylesheet" type="text/css" href="login.css"/>
</head>
<body style="display: block; padding: 50px;">
    <div class="form" style="margin: 0 auto; width: 50%; text-align: center;">
    <?php
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['nome'])) {
            $nome = $_POST['nome'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $conferma = $_POST['conferma_password'];

            // Controllo lato server corrispondenza password
            if ($password !== $conferma) {
                echo "<h2>Errore</h2>";
                echo "<p>Le password non coincidono. <a href='signup.php'>Riprova</a></p>";
                exit;
            }

            // 1. Controlliamo se l'username esiste già
            if (user_exists($username, $db)) {
                echo "<h2>Attenzione</h2>";
                echo "<p>L'username <strong>" . htmlspecialchars($username) . "</strong> è già in uso.</p>";
                echo "<input type='button' value='Scegline un altro' onclick=\"window.location.href='signup.php'\" class='button'/>";
            } else {
                // 2. Registriamo il nuovo utente
                // Creiamo l'hash della password (compatibile con password_verify del login)
                $hash_password = password_hash($password, PASSWORD_DEFAULT);

                if (register_user($nome, $username, $hash_password, $db)) {
                    echo "<h2>Benvenuto a bordo!</h2>";
                    echo "<p>Registrazione completata con successo.</p>";
                    echo "<p>Ora puoi effettuare l'accesso.</p>";
                    echo "<br/>";
                    echo "<input type='button' value='Vai al Login' onclick=\"window.location.href='login.html'\" class='button'/>";
                } else {
                    echo "<h2>Errore di Sistema</h2>";
                    echo "<p>Non è stato possibile completare la registrazione. Riprova più tardi.</p>";
                    echo "<a href='signup.php'>Torna indietro</a>";
                }
            }

        } else {
            echo "<p>Errore: Dati mancanti. <a href='signup.php'>Riprova</a></p>";
        }
    ?>
    </div>
</body>
</html>

<?php
/**
 * Controlla se l'username esiste già nel DB
 */
function user_exists($user, $db) {
    $sql = "SELECT username FROM account WHERE username = $1";
    $stmt_name = "checkUserExists";
    
    // Prepariamo lo statement solo se non esiste già (per evitare errori in loop multipli, anche se qui è sequenziale)
    // Nota: pg_prepare fallisce se il nome esiste già nella stessa connessione, 
    // quindi usiamo un @ o gestiamo l'errore se necessario, oppure nomi univoci.
    // Per semplicità qui usiamo pg_query_params che non richiede prepare esplicito nome
    
    $ret = pg_query_params($db, $sql, array($user));

    if (!$ret) {
        // Gestione errore query
        return false; 
    }

    if (pg_num_rows($ret) > 0) {
        return true; // Esiste
    } else {
        return false; // Non esiste
    }
}

/**
 * Inserisce il nuovo utente nel DB
 */
function register_user($nome, $username, $password, $db) {
    // Assumiamo che la tabella 'account' abbia colonne: nome, username, password (e id autoincrement)
    $sql = "INSERT INTO account (nome, username, password) VALUES ($1, $2, $3)";
    
    $ret = pg_query_params($db, $sql, array($nome, $username, $password));

    if ($ret) {
        return true;
    } else {
        echo "";
        return false;
    }
}
?>