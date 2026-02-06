<?php
include 'db.php';
session_start();
?>
<html>
<head>
	<title>Gestione Login</title>
</head>
<body>
	<?php


		if($_POST['username'] || $_POST['password']){
			$username =  $_POST['username'];
			$pass =  $_POST['password'];
			//chiama la funzione get_pwd che controlla
			//se username esiste nel DB. Se esiste, restituisce la password (hash), altrimenti restituisce false.
			//$hash = get_pwd($user,$db);
			// Recupero intera riga utente
   			$userRow = get_user($username, $db);
			if(!$userRow){
				echo "<p> L'utente non esiste. <a href=\"login.html\">Riprova</a></p>";
			}
			else{
				if(password_verify($pass, $userRow['password'])){
					echo "<p>Login Eseguito con successo</p>";
					//Se il login è corretto, inizializziamo la sessione
					
					$_SESSION['utente']=$userRow['username'];
                    $_SESSION['utente_id'] = $userRow['id'];
					$_SESSION['nome'] = $userRow['nome'];
        			header("Location: index2.php");
       				 exit;
					//echo "<p><a href=\"reserved.php\">Accedi</a> al contenuto riservato solo agli utenti registrati<p>";
				}
				else{
					echo 'Username o password errati. <a href="login.html">Riprova</a>';
				}
			}
		}
		else{
			echo "<p>ERRORE: username o password non inseriti <a href=\"login.html\">Riprova</a></p>";
			exit();
		}
	?>
</body>
</html>

<?php
	
	function get_user($user, $db){
    $sql = "SELECT id, nome, username, password 
            FROM account 
            WHERE username = $1;";
    $prep = pg_prepare($db, "sqlUser", $sql); 
    $ret = pg_execute($db, "sqlUser", array($user));

    if (!$ret) {
        echo "ERRORE QUERY: " . pg_last_error($db);
        return false;
    }

    if ($row = pg_fetch_assoc($ret)) {
        return $row; // restituiamo TUTTA la riga
    } else {
        return false;
    }
}
?>
