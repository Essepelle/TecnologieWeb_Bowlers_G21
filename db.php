<?php
$host = 'localhost';
$port = '5432';
$db = 'gruppo21';
$username = 'www';
$password = 'www';

$connection_string = "host=$host port=$port dbname=$db user=$username password=$password";


//CONNESSIONE AL DB
$db = pg_connect($connection_string);

if (!$db) {
    die('Errore critico: Impossibile connettersi al database PostgreSQL. ' . pg_last_error());
}

?>