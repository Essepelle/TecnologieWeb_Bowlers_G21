<?php
$host = 'localhost';
$port = '5432';
$db = 'TW';
$username = 'www';
$password = 'www';

$connection_string = "host=$host port=$port dbname=$db user=$username password=$password";


//CONNESSIONE AL DB
$db = pg_connect($connection_string) or die('Impossibile connetersi al database: ' . pg_last_error());


/*
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Errore connessione DB: " . $conn->connect_error);
}
?>
*/