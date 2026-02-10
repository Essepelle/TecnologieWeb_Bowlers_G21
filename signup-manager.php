<?php
include 'db.php';
session_start();
?>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Esito Registrazione - The Bowler Club</title>
    <link rel="stylesheet" type="text/css" href="login.css"/>
</head>
<body style="display: block; padding: 50px;">
    <div class="form" style="margin: 0 auto; width: 50%; text-align: center;">
    <?php
        // Verifichiamo la ricezione di tutti i dati dal form HTML
        if (isset($_POST['username'], $_POST['password'], $_POST['nome'], $_POST['email'])) {
            
            $nome_completo = $_POST['nome'];
            $email         = $_POST['email'];
            $username      = $_POST['username'];
            $password      = $_POST['password'];
            $conferma      = $_POST['conferma_password'];

            // 1. Controllo corrispondenza password
            if ($password !== $conferma) {
                echo "<h2>Errore</h2><p>Le password non coincidono. <a href='signup.html'>Riprova</a></p>";
                exit;
            }

            // 2. Controllo se l'utente esiste già
            if (user_exists($username, $db)) {
                echo "<h2>Attenzione</h2>";
                echo "<p>L'username <strong>" . htmlspecialchars($username) . "</strong> è già in uso.</p>";
                echo "<input type='button' value='Scegline un altro' onclick=\"window.location.href='signup.html'\" class='button'/>";
            } else {
                // 3. Registrazione nuovo utente
                $hash_password = password_hash($password, PASSWORD_DEFAULT);

                if (register_user($username, $nome_completo, $email, $hash_password, $db)) {
                    echo "<h2>Benvenuto a bordo!</h2>";
                    echo "<p>Registrazione completata con successo.</p>";
                    echo "<br/><input type='button' value='Vai al Login' onclick=\"window.location.href='login.html'\" class='button'/>";
                } else {
                    echo "<h2>Errore di Sistema</h2>";
                    echo "<p>Non è stato possibile completare l'operazione. Verifica la connessione al database.</p>";
                    echo "<a href='signup.html'>Torna alla registrazione</a>";
                }
            }
        } else {
            echo "<p>Errore: Dati mancanti dal modulo. <a href='signup.html'>Riprova</a></p>";
        }
    ?>
    </div>
</body>
</html>

<?php
/**
 * Verifica l'esistenza dello username (PK)
 */
function user_exists($user, $db) {
    // Nota: PostgreSQL è case-sensitive, assicurati che la tabella si chiami 'utenti'
    $sql = "SELECT username FROM utenti WHERE username = $1";
    $ret = pg_query_params($db, $sql, array($user));
    if ($ret && pg_num_rows($ret) > 0) return true;
    return false;
}

/**
 * Inserisce i dati usando i nomi colonne esatti del tuo screenshot:
 * username, nome_completo, email, pass
 */
function register_user($username, $nome, $email, $pwd, $db) {
    // Query adeguata ai nomi colonne: nome_completo e pass
    $sql = "INSERT INTO utenti (username, nome_completo, email, pass) VALUES ($1, $2, $3, $4)";
    $ret = pg_query_params($db, $sql, array($username, $nome, $email, $pwd));
    return (bool)$ret;
}
?>