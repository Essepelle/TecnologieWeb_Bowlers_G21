<?php

/* Dichiarazione delle variabili di configurazione necessarie per identificare 
   il server, la porta di ascolto, il nome del database e le credenziali di accesso (username e password) 
   utilizzate per stabilire la comunicazione con PostgreSQL. */
$host = 'localhost';
$port = '5432';
$db = 'gruppo21';
$username = 'www';
$password = 'www';

$connection_string = "host=$host port=$port dbname=$db user=$username password=$password";


/* --- CONNESSIONE AL DB ---
Utilizzo la funzione pg_connect per aprire effettivamente il tunnel di comunicazione con il database. 
Salvo il riferimento alla connessione nella variabile $db, che userò come "chiave" per ogni futura query. */
$db = pg_connect($connection_string);

/* Se la connessione fallisce (restituendo false), interrompo immediatamente
   e stampo l'errore tecnico specifico fornito dal sistema. */
if (!$db) {
    die('Errore critico: Impossibile connettersi al database PostgreSQL. ' . pg_last_error());
}

?>