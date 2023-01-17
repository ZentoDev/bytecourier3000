<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_admin.php");


function stampaUser() {

  require_once("../../mysql/connection.php");
  if( !$connection_mysqli )   return 'Accesso al database non riuscito';  //problemi di connessione al db, return -1

  //query per verificare l'esistenza di un utente con lo stesso username
  $select_query = "SELECT * FROM $user_table_name";

  if ( $res = mysqli_query($connection_mysqli, $select_query) ) {

    $table="<table>";  
		while ( $row = mysqli_fetch_array($res) ) {  //per ogni elemento dell'array

            if($row['permesso'] == 10 || $row['permesso'] == 100) {
                if($row['permesso'] == 10) $tipologia_utente = 'bite courier';
                if($row['permesso'] == 100) $tipologia_utente = 'gestore';
                $table.="<tr>
                <th><strong></th>
                <td><strong>Username:</strong> $row[username]<br />
                <strong>Password:</strong> $row[password]<br />
                <strong>Tipologia:</strong> $tipologia_utente<br />
                </td>
                <td>
        
                <strong>Nome:</strong> $row[nome]<br />
                <strong>Cognome:</strong> $row[cognome]<br />
                <strong>Data di nascita:</strong> $row[data_nascita]<br />
                <strong>Ban:</strong> $row[ban]
                </td>
                <td>
                <form action=\"modifica_utente_avanzato.php\" method=\"post\">
                <input type=\"hidden\" name=\"username\" value=\"{$row['username']}\">
                <input type=\"hidden\" name=\"password\" value=\"{$row['password']}\">
                <input type=\"hidden\" name=\"nome\" value=\"{$row['nome']}\">
                <input type=\"hidden\" name=\"cognome\" value=\"{$row['cognome']}\">
                <input type=\"hidden\" name=\"data\" value=\"{$row['data_nascita']}\">
                <input type=\"hidden\" name=\"permesso_user\" value=\"{$row['permesso']}\">
                <input type=\"hidden\" name=\"ban\" value=\"{$row['ban']}\">
                <button type=\"submit\" name=\"invio_dati\" value=\"modifica\" >Modifica utente</button>
                </form>
                </td>
                </tr>";
            }
        }

    $table.="</table>";
    return $table;
  }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Gestione utenti avanzati</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	  <link rel="stylesheet" href="../style1.css" type="text/css">
	  <link rel="stylesheet" href="../tabselezione.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
	
</div>

<div id="content">
   <div id="center" class="colonna">
     <h2>Gestione utenti avanzati</h2>

     <?php echo stampaUser(); ?>
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_admin.php");?>
   </div>
</div>


</body>
</html>