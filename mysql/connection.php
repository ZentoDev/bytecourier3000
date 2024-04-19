<?php  

// Variabili con all'interno il nome del database e delle tabelle
$DB_host="localhost";
$DB_user="root";
$DB_pass="";
$DB_name="dalia_bytecourier3000";
$user_table_name="users";

/*Creo un oggetto che rappresenta la connessione al server mysql                      
La funzione mysqli ha per argomenti:
-il nome dell'host
-l'username dell'account mysql
-la rispettiva password
-il nome del database al quale dobbiamo connetterci*/ 

try{
    $connection_mysqli = new mysqli($DB_host, $DB_user, $DB_pass, $DB_name);
}catch (Exception $e){ $connection_mysqli = NULL; }

/* Verifico se la connessione al database è andata a buon fine.
In caso di errore restituisco un valore nullo. */

?>